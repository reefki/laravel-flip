<?php

namespace Reefki\Flip\Resources;

class Bill extends Resource
{
    /**
     * Create a payment / bill link.
     *
     * Endpoint: `POST /{version}/pwf/bill`.
     *
     * @param  array<string, mixed>  $payload  See Flip docs for the full field list. Required:
     *     `title`, `type` (`SINGLE`|`MULTIPLE`), `expired_date`, `step`
     *     (`checkout`|`checkout_seamless`|`direct_api`). For
     *     `checkout_seamless` and `direct_api` the `sender_*` fields are also
     *     required.
     * @return array<string, mixed>
     */
    public function create(array $payload): array
    {
        return $this->client->postJson($this->path('pwf/bill'), $this->compact($payload));
    }

    /**
     * List every existing bill / payment link.
     *
     * Endpoint: `GET /{version}/pwf/bill`.
     *
     * @return array<string, mixed>
     */
    public function list(): array
    {
        return $this->client->get($this->path('pwf/bill'));
    }

    /**
     * Fetch a single bill by id.
     *
     * Endpoint: `GET /{version}/pwf/{billId}/bill`.
     *
     * @param  string|int  $billId  Flip's bill link id.
     * @return array<string, mixed>
     */
    public function find(string|int $billId): array
    {
        return $this->client->get($this->path("pwf/{$billId}/bill"));
    }

    /**
     * Edit a bill. Only bills created with step `checkout` or
     * `checkout_seamless` can be edited.
     *
     * Endpoint: `PUT /{version}/pwf/{billId}/bill`.
     *
     * @param  string|int  $billId  Flip's bill link id.
     * @param  array<string, mixed>  $payload  Fields to update.
     * @return array<string, mixed>
     */
    public function update(string|int $billId, array $payload): array
    {
        return $this->client->putJson($this->path("pwf/{$billId}/bill"), $this->compact($payload));
    }
}
