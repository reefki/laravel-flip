<?php

namespace Reefki\Flip;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Reefki\Flip\Exceptions\AuthenticationException;
use Reefki\Flip\Exceptions\FlipException;
use Reefki\Flip\Exceptions\MaintenanceException;
use Reefki\Flip\Exceptions\NotFoundException;
use Reefki\Flip\Exceptions\ValidationException;

class Client
{
    /**
     * Laravel HTTP client factory used to build outgoing requests.
     *
     * @var \Illuminate\Http\Client\Factory
     */
    protected HttpFactory $http;

    /**
     * Resolved Flip configuration array (typically `config('flip')`).
     *
     * @var array<string, mixed>
     */
    protected array $config;

    /**
     * Build a new low-level Flip HTTP client.
     *
     * @param  \Illuminate\Http\Client\Factory  $http  Laravel HTTP factory.
     * @param  array<string, mixed>  $config  Resolved Flip config.
     */
    public function __construct(HttpFactory $http, array $config)
    {
        $this->http = $http;
        $this->config = $config;
    }

    /**
     * The default API version (`v2` or `v3`) used by multi-version resources.
     *
     * @return string
     */
    public function defaultVersion(): string
    {
        return (string) ($this->config['version'] ?? 'v3');
    }

    /**
     * Resolve the base URL for the configured environment.
     *
     * @return string
     */
    public function baseUrl(): string
    {
        $env = $this->config['environment'] ?? 'sandbox';

        return rtrim($this->config['base_urls'][$env] ?? $this->config['base_urls']['sandbox'], '/');
    }

    /**
     * The Flip for Business API secret key used for HTTP basic auth.
     *
     * @return string|null
     */
    public function secretKey(): ?string
    {
        return $this->config['secret_key'] ?? null;
    }

    /**
     * The validation token used to verify incoming webhook callbacks.
     *
     * @return string|null
     */
    public function validationToken(): ?string
    {
        return $this->config['validation_token'] ?? null;
    }

    /**
     * Issue a GET request and decode the JSON response.
     *
     * @param  string  $path  Path beginning with the version segment, e.g. `v3/general/balance`.
     * @param  array<string, mixed>  $query  Query string parameters.
     * @param  array<string, string>  $headers  Additional request headers.
     * @return array<string, mixed>
     */
    public function get(string $path, array $query = [], array $headers = []): array
    {
        return $this->send('GET', $path, query: $query, headers: $headers);
    }

    /**
     * Issue a POST request with `application/x-www-form-urlencoded` body.
     *
     * @param  string  $path  Path beginning with the version segment.
     * @param  array<string, mixed>  $body  Form fields.
     * @param  array<string, string>  $headers  Additional request headers.
     * @return array<string, mixed>
     */
    public function postForm(string $path, array $body = [], array $headers = []): array
    {
        return $this->send('POST', $path, body: $body, headers: $headers, asJson: false);
    }

    /**
     * Issue a POST request with a JSON body.
     *
     * @param  string  $path  Path beginning with the version segment.
     * @param  array<string, mixed>  $body  JSON-encodable payload.
     * @param  array<string, string>  $headers  Additional request headers.
     * @return array<string, mixed>
     */
    public function postJson(string $path, array $body = [], array $headers = []): array
    {
        return $this->send('POST', $path, body: $body, headers: $headers, asJson: true);
    }

    /**
     * Issue a PUT request with a JSON body.
     *
     * @param  string  $path  Path beginning with the version segment.
     * @param  array<string, mixed>  $body  JSON-encodable payload.
     * @param  array<string, string>  $headers  Additional request headers.
     * @return array<string, mixed>
     */
    public function putJson(string $path, array $body = [], array $headers = []): array
    {
        return $this->send('PUT', $path, body: $body, headers: $headers, asJson: true);
    }

    /**
     * Build a pre-configured pending request (auth, base URL, timeouts).
     *
     * Exposed for advanced consumers that need to send a one-off request
     * outside of the resource layer (e.g. arbitrary endpoints).
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    public function pendingRequest(): PendingRequest
    {
        $http = $this->config['http'] ?? [];
        $request = $this->http
            ->withBasicAuth((string) $this->secretKey(), '')
            ->baseUrl($this->baseUrl())
            ->timeout((int) ($http['timeout'] ?? 30))
            ->connectTimeout((int) ($http['connect_timeout'] ?? 10))
            ->acceptJson();

        $retry = (int) ($http['retry_times'] ?? 0);
        if ($retry > 0) {
            $request = $request->retry($retry, (int) ($http['retry_sleep_ms'] ?? 200));
        }

        return $request;
    }

    /**
     * Send a request and decode the JSON response, mapping HTTP errors to
     * typed exceptions.
     *
     * @param  string  $method  HTTP verb.
     * @param  string  $path  Path beginning with the version segment.
     * @param  array<string, mixed>  $query  Query string parameters.
     * @param  array<string, mixed>  $body  Request body.
     * @param  array<string, string>  $headers  Additional request headers.
     * @param  bool  $asJson  When true, encode the body as JSON; otherwise as form data.
     * @return array<string, mixed>
     *
     * @throws \Reefki\Flip\Exceptions\FlipException
     */
    protected function send(
        string $method,
        string $path,
        array $query = [],
        array $body = [],
        array $headers = [],
        bool $asJson = false,
    ): array {
        $request = $this->pendingRequest();

        if ($headers !== []) {
            $request = $request->withHeaders($headers);
        }

        if ($asJson) {
            $request = $request->asJson();
        } else {
            $request = $request->asForm();
        }

        $url = '/' . ltrim($path, '/');
        if ($query !== []) {
            $url .= '?' . http_build_query(array_filter(
                $query,
                static fn ($v) => $v !== null && $v !== ''
            ));
        }

        /** @var \Illuminate\Http\Client\Response $response */
        $response = match ($method) {
            'GET' => $request->get($url),
            'POST' => $request->post($url, $body),
            'PUT' => $request->put($url, $body),
            default => $request->send($method, $url, ['form_params' => $body]),
        };

        if ($response->successful()) {
            $data = $response->json();

            return is_array($data) ? $data : ['data' => $data];
        }

        throw $this->mapError($response);
    }

    /**
     * Translate a non-2xx response to the most specific FlipException subclass.
     *
     * @param  \Illuminate\Http\Client\Response  $response
     * @return \Reefki\Flip\Exceptions\FlipException
     */
    protected function mapError(Response $response): FlipException
    {
        return match ($response->status()) {
            401 => AuthenticationException::fromResponse($response),
            404 => NotFoundException::fromResponse($response),
            422 => ValidationException::fromResponse($response),
            503 => MaintenanceException::fromResponse($response),
            default => FlipException::fromResponse($response),
        };
    }
}
