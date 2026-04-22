<?php

namespace Reefki\Flip\Resources;

class Payment extends Resource
{
    /**
     * List payments belonging to a single bill.
     *
     * Endpoint: `GET /{version}/pwf/{billId}/payment`.
     *
     * @param  string|int  $billId  Flip's bill link id.
     * @param  array<string, scalar|null>  $filters  Any of: start_date,
     *     end_date, pagination, page, sort_by, sort_type.
     * @return array<string, mixed>
     */
    public function forBill(string|int $billId, array $filters = []): array
    {
        $id = rawurlencode((string) $billId);

        return $this->client->get($this->path("pwf/{$id}/payment"), $filters);
    }

    /**
     * List every payment across every bill.
     *
     * Endpoint: `GET /{version}/pwf/payment`.
     *
     * @param  array<string, scalar|null>  $filters  Any of: start_date,
     *     end_date, pagination, page, sort_by, sort_type, reference_id.
     * @return array<string, mixed>
     */
    public function list(array $filters = []): array
    {
        return $this->client->get($this->path('pwf/payment'), $filters);
    }
}
