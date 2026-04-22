<?php

namespace Reefki\Flip\Resources;

class Payment extends Resource
{
    /**
     * List payments belonging to a single bill.
     *
     * Pinned to v3 — Flip's Accept Payment v2 is deprecated; only v3 is
     * documented today.
     *
     * Endpoint: `GET /v3/pwf/{billId}/payment`.
     *
     * @param  string|int  $billId  Flip's bill link id.
     * @param  array<string, scalar|null>  $filters  Any of: start_date,
     *     end_date, pagination, page, sort_by, sort_type.
     * @return array<string, mixed>
     *
     * @throws \Reefki\Flip\Exceptions\FlipException
     */
    public function forBill(string|int $billId, array $filters = []): array
    {
        $id = rawurlencode((string) $billId);

        return $this->client->get($this->path("pwf/{$id}/payment", 'v3'), $filters);
    }

    /**
     * List every payment across every bill.
     *
     * Pinned to v3 — see `forBill()`.
     *
     * Endpoint: `GET /v3/pwf/payment`.
     *
     * @param  array<string, scalar|null>  $filters  Any of: start_date,
     *     end_date, pagination, page, sort_by, sort_type, reference_id.
     * @return array<string, mixed>
     *
     * @throws \Reefki\Flip\Exceptions\FlipException
     */
    public function list(array $filters = []): array
    {
        return $this->client->get($this->path('pwf/payment', 'v3'), $filters);
    }
}
