<?php

namespace Reefki\Flip\Resources;

class Disbursement extends Resource
{
    /**
     * Create a money-transfer disbursement.
     *
     * Endpoint: `POST /{version}/disbursement`.
     *
     * @param  array{
     *     account_number: string,
     *     bank_code: string,
     *     amount: int|string,
     *     remark?: string,
     *     recipient_city?: int,
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
            $this->path('disbursement'),
            $this->compact($payload),
            $this->compact(['idempotency-key' => $idempotencyKey, 'X-TIMESTAMP' => $timestamp]),
        );
    }

    /**
     * List disbursements with optional filters.
     *
     * Endpoint: `GET /{version}/disbursement`.
     *
     * @param  array<string, scalar|null>  $filters  Any of: pagination, page, sort, id,
     *     amount, status, timestamp, bank_code, recipient_name, remark,
     *     time_served, created_from, direction.
     * @return array<string, mixed>
     *
     * @throws \Reefki\Flip\Exceptions\FlipException
     */
    public function list(array $filters = []): array
    {
        return $this->client->get($this->path('disbursement'), $filters);
    }

    /**
     * Fetch a single disbursement by Flip's transaction id.
     *
     * Endpoint: `GET /{version}/get-disbursement?id=...`.
     *
     * @param  string|int  $id  Flip's transaction id.
     * @return array<string, mixed>
     *
     * @throws \Reefki\Flip\Exceptions\FlipException
     */
    public function find(string|int $id): array
    {
        return $this->client->get($this->path('get-disbursement'), ['id' => (string) $id]);
    }

    /**
     * Fetch a disbursement by your own idempotency key.
     *
     * Endpoint: `GET /{version}/get-disbursement?idempotency-key=...`.
     *
     * @param  string  $key  Idempotency key originally supplied to `create()`.
     * @return array<string, mixed>
     *
     * @throws \Reefki\Flip\Exceptions\FlipException
     */
    public function findByIdempotencyKey(string $key): array
    {
        return $this->client->get($this->path('get-disbursement'), ['idempotency-key' => $key]);
    }

    /**
     * Look up the bank account holder name. Pinned to v2 because Flip has not
     * shipped a v3 of this endpoint.
     *
     * If the result isn't yet cached, the response will be `PENDING` and the
     * final value will arrive via the configured callback URL.
     *
     * Endpoint: `POST /v2/disbursement/bank-account-inquiry`.
     *
     * @param  string  $accountNumber  Account number to look up.
     * @param  string  $bankCode  Flip bank code.
     * @param  string|null  $inquiryKey  Optional alphanumeric key for matching async callbacks.
     * @return array<string, mixed>
     *
     * @throws \Reefki\Flip\Exceptions\FlipException
     */
    public function inquiry(string $accountNumber, string $bankCode, ?string $inquiryKey = null): array
    {
        return $this->client->postForm(
            $this->path('disbursement/bank-account-inquiry', 'v2'),
            $this->compact([
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
                'inquiry_key' => $inquiryKey,
            ]),
        );
    }
}
