<?php

namespace Reefki\Flip\Resources;

class Bill extends Resource
{
    /**
     * Create a payment / bill link.
     *
     * Pinned to v3 — Flip's v2 Accept Payment is deprecated; only v3 is
     * documented today and the request shape (JSON body, string `step`) does
     * not match v2 anyway.
     *
     * Endpoint: `POST /v3/pwf/bill`.
     *
     * @param  array<string, mixed>  $payload  See Flip docs for the full field list. Required:
     *     `title`, `type` (`SINGLE`|`MULTIPLE`), `expired_date`, `step`
     *     (`checkout`|`checkout_seamless`|`direct_api`). For
     *     `checkout_seamless` and `direct_api` the `sender_*` fields are also
     *     required.
     * @return array<string, mixed>
     *
     * @throws \Reefki\Flip\Exceptions\FlipException
     */
    public function create(array $payload): array
    {
        return $this->client->postJson($this->path('pwf/bill', 'v3'), $this->compact($payload));
    }

    /**
     * List every existing bill / payment link.
     *
     * Pinned to v3 — see `create()`.
     *
     * Endpoint: `GET /v3/pwf/bill`.
     *
     * @return array<string, mixed>
     *
     * @throws \Reefki\Flip\Exceptions\FlipException
     */
    public function list(): array
    {
        return $this->client->get($this->path('pwf/bill', 'v3'));
    }

    /**
     * Fetch a single bill by id.
     *
     * Pinned to v3 — see `create()`.
     *
     * Endpoint: `GET /v3/pwf/{billId}/bill`.
     *
     * @param  string|int  $billId  Flip's bill link id.
     * @return array<string, mixed>
     *
     * @throws \Reefki\Flip\Exceptions\FlipException
     */
    public function find(string|int $billId): array
    {
        $id = rawurlencode((string) $billId);

        return $this->client->get($this->path("pwf/{$id}/bill", 'v3'));
    }

    /**
     * Edit a bill. Only bills created with step `checkout` or
     * `checkout_seamless` can be edited.
     *
     * Pinned to v3 — see `create()`.
     *
     * Endpoint: `PUT /v3/pwf/{billId}/bill`.
     *
     * @param  string|int  $billId  Flip's bill link id.
     * @param  array<string, mixed>  $payload  Fields to update.
     * @return array<string, mixed>
     *
     * @throws \Reefki\Flip\Exceptions\FlipException
     */
    public function update(string|int $billId, array $payload): array
    {
        $id = rawurlencode((string) $billId);

        return $this->client->putJson($this->path("pwf/{$id}/bill", 'v3'), $this->compact($payload));
    }
}
