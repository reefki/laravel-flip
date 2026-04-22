<?php

namespace Reefki\Flip\Resources;

class InternationalDisbursement extends Resource
{
    /**
     * Fetch current exchange rates plus minimum/maximum amounts, cut-off
     * times and fees per corridor.
     *
     * Pinned to v2 — Flip has not shipped a v3 equivalent.
     *
     * Endpoint: `GET /v2/international-disbursement/exchange-rates`.
     *
     * @param  string  $transactionType  One of `C2C`, `C2B`, `B2B`, `B2C`.
     * @param  string|null  $countryIsoCode  ISO 3166 alpha-3 code, comma-separated for multiple.
     * @return array<string, mixed>
     */
    public function exchangeRates(string $transactionType, ?string $countryIsoCode = null): array
    {
        return $this->client->get(
            $this->path('international-disbursement/exchange-rates', 'v2'),
            $this->compact([
                'transaction_type' => $transactionType,
                'country_iso_code' => $countryIsoCode,
            ]),
        );
    }

    /**
     * Fetch the required form fields (purpose of remittance, source of fund,
     * beneficiary bank details, ...) for a given corridor.
     *
     * Pinned to v2 — Flip has not shipped a v3 equivalent.
     *
     * Endpoint: `GET /v2/international-disbursement/form-data`.
     *
     * @param  string  $transactionType  One of `C2C`, `C2B`, `B2B`, `B2C`.
     * @param  string|null  $countryIsoCode  ISO 3166 alpha-3 code, comma-separated for multiple.
     * @return array<string, mixed>
     */
    public function formData(string $transactionType, ?string $countryIsoCode = null): array
    {
        return $this->client->get(
            $this->path('international-disbursement/form-data', 'v2'),
            $this->compact([
                'transaction_type' => $transactionType,
                'country_iso_code' => $countryIsoCode,
            ]),
        );
    }

    /**
     * Fetch a single international transfer transaction.
     *
     * Pinned to v2 — Flip has not shipped a v3 equivalent.
     *
     * Endpoint: `GET /v2/international-disbursement/{transactionId}`.
     *
     * @param  string|int  $transactionId  Flip's transaction id.
     * @return array<string, mixed>
     */
    public function find(string|int $transactionId): array
    {
        $id = rawurlencode((string) $transactionId);

        return $this->client->get($this->path("international-disbursement/{$id}", 'v2'));
    }

    /**
     * List international transfer transactions with optional filters.
     *
     * Pinned to v2 — Flip has not shipped a v3 equivalent.
     *
     * Endpoint: `GET /v2/international-disbursement`.
     *
     * @param  array<string, scalar|null>  $filters  Any of: pagination, page, sort_by, ...
     * @return array<string, mixed>
     */
    public function list(array $filters = []): array
    {
        return $this->client->get($this->path('international-disbursement', 'v2'), $filters);
    }

    /**
     * Create a C2C / C2B international transfer.
     *
     * Pinned to v2 — Flip has not shipped a v3 equivalent.
     *
     * Endpoint: `POST /v2/international-disbursement`.
     *
     * @param  array<string, mixed>  $payload  See Flip docs for the full field list.
     * @param  string  $idempotencyKey  Required `idempotency-key` header value.
     * @param  string|null  $timestamp  Optional ISO 8601 `X-TIMESTAMP` header value.
     * @return array<string, mixed>
     */
    public function createConsumer(array $payload, string $idempotencyKey, ?string $timestamp = null): array
    {
        return $this->client->postForm(
            $this->path('international-disbursement', 'v2'),
            $this->compact($payload),
            $this->compact(['idempotency-key' => $idempotencyKey, 'X-TIMESTAMP' => $timestamp]),
        );
    }

    /**
     * Create a B2C / B2B international transfer (typically requires an attachment).
     *
     * Pinned to v2 — Flip has not shipped a v3 equivalent.
     *
     * Endpoint: `POST /v2/international-disbursement/create-with-attachment`.
     *
     * @param  array<string, mixed>  $payload  See Flip docs for the full field list.
     * @param  string  $idempotencyKey  Required `idempotency-key` header value.
     * @param  string|null  $timestamp  Optional ISO 8601 `X-TIMESTAMP` header value.
     * @return array<string, mixed>
     */
    public function createBusiness(array $payload, string $idempotencyKey, ?string $timestamp = null): array
    {
        return $this->client->postForm(
            $this->path('international-disbursement/create-with-attachment', 'v2'),
            $this->compact($payload),
            $this->compact(['idempotency-key' => $idempotencyKey, 'X-TIMESTAMP' => $timestamp]),
        );
    }
}
