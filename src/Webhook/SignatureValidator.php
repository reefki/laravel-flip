<?php

namespace Reefki\Flip\Webhook;

use Illuminate\Http\Request;
use Reefki\Flip\Exceptions\InvalidWebhookSignatureException;

class SignatureValidator
{
    /**
     * Configured validation token from the Flip for Business dashboard.
     *
     * @var string
     */
    protected string $expectedToken;

    /**
     * Build a new webhook signature validator.
     *
     * @param  string  $expectedToken  Validation token from the Flip dashboard.
     */
    public function __construct(string $expectedToken)
    {
        $this->expectedToken = $expectedToken;
    }

    /**
     * Compare an incoming token to the configured validation token using a
     * timing-safe comparison.
     *
     * @param  string|null  $token  Token received from the callback.
     * @return bool
     */
    public function isValid(?string $token): bool
    {
        if ($this->expectedToken === '' || $token === null || $token === '') {
            return false;
        }

        return hash_equals($this->expectedToken, $token);
    }

    /**
     * Validate the `token` field on an incoming HTTP request.
     *
     * @param  \Illuminate\Http\Request  $request  Inbound webhook request.
     * @return bool
     */
    public function isValidRequest(Request $request): bool
    {
        return $this->isValid((string) $request->input('token'));
    }

    /**
     * Verify the request signature and return the decoded `data` payload.
     *
     * @param  \Illuminate\Http\Request  $request  Inbound webhook request.
     * @return array<string, mixed>
     *
     * @throws \Reefki\Flip\Exceptions\InvalidWebhookSignatureException
     */
    public function verify(Request $request): array
    {
        if (! $this->isValidRequest($request)) {
            throw new InvalidWebhookSignatureException('Flip webhook validation token mismatch.');
        }

        $raw = (string) $request->input('data', '');
        $decoded = json_decode($raw, true);

        if (! is_array($decoded)) {
            throw new InvalidWebhookSignatureException('Flip webhook `data` field is not valid JSON.');
        }

        return $decoded;
    }
}
