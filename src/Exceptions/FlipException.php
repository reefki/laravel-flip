<?php

namespace Reefki\Flip\Exceptions;

use Illuminate\Http\Client\Response;
use RuntimeException;
use Throwable;

class FlipException extends RuntimeException
{
    /**
     * Decoded JSON body returned by Flip with the failed response.
     *
     * @var array<string, mixed>
     */
    protected array $payload = [];

    /**
     * Original HTTP response when the exception was raised from one.
     *
     * @var \Illuminate\Http\Client\Response|null
     */
    protected ?Response $response = null;

    /**
     * Build a new Flip exception.
     *
     * @param  string  $message  Human-readable error message.
     * @param  int  $code  HTTP status code (or 0 when not applicable).
     * @param  \Throwable|null  $previous  Underlying exception, if any.
     */
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Build an exception of the calling subclass from a Flip HTTP response.
     *
     * @param  \Illuminate\Http\Client\Response  $response
     * @return static
     */
    public static function fromResponse(Response $response): static
    {
        $body = $response->json();
        $message = $body['message']
            ?? $body['name']
            ?? ($body['code'] ?? 'Flip API request failed');

        $exception = new static((string) $message, $response->status());
        $exception->response = $response;
        $exception->payload = is_array($body) ? $body : [];

        return $exception;
    }

    /**
     * The originating HTTP response.
     *
     * @return \Illuminate\Http\Client\Response|null
     */
    public function response(): ?Response
    {
        return $this->response;
    }

    /**
     * The decoded response body.
     *
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * Convenience accessor for Flip's structured validation errors.
     *
     * @return array<int, array{attribute: string, code: int, message: string}>
     */
    public function errors(): array
    {
        return $this->payload['errors'] ?? [];
    }
}
