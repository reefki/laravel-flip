<?php

namespace Reefki\Flip\Resources;

class SpecialDisbursement extends Resource
{
    /**
     * Create a special money-transfer disbursement on behalf of a PJP company's
     * end user. Sender details are required because the original sender is
     * the company's customer, not the company itself.
     *
     * Endpoint: `POST /{version}/special-disbursement`.
     *
     * @param  array{
     *     account_number: string,
     *     bank_code: string,
     *     amount: int|string,
     *     sender_name: string,
     *     sender_address: string,
     *     sender_country: int,
     *     sender_job: string,
     *     direction: string,
     *     remark?: string,
     *     recipient_city?: int,
     *     sender_place_of_birth?: int,
     *     sender_date_of_birth?: string,
     *     sender_identity_type?: string,
     *     sender_identity_number?: string,
     *     beneficiary_email?: string,
     * }  $payload  Disbursement payload.
     * @param  string  $idempotencyKey  Required `idempotency-key` header value.
     * @param  string|null  $timestamp  Optional ISO 8601 `X-TIMESTAMP` header value.
     * @return array<string, mixed>
     *
     * @throws \Reefki\Flip\Exceptions\FlipException
     */
    public function create(array $payload, string $idempotencyKey, ?string $timestamp = null): array
    {
        return $this->client->postForm(
            $this->path('special-disbursement'),
            $this->compact($payload),
            $this->compact(['idempotency-key' => $idempotencyKey, 'X-TIMESTAMP' => $timestamp]),
        );
    }
}
