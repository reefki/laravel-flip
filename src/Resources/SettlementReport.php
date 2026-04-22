<?php

namespace Reefki\Flip\Resources;

class SettlementReport extends Resource
{
    /**
     * Kick off generation of a settlement report for a date range.
     *
     * Returns a `request_id` you can poll with `checkStatus()`.
     *
     * Endpoint: `POST /{version}/settlement-report/generate`.
     *
     * @param  string  $startDate  Inclusive start date in `YYYY-MM-DD` format.
     * @param  string  $endDate  Inclusive end date in `YYYY-MM-DD` format.
     * @return array<string, mixed>
     */
    public function generate(string $startDate, string $endDate): array
    {
        return $this->client->postJson(
            $this->path('settlement-report/generate'),
            [
                'settlement_date_start' => $startDate,
                'settlement_date_end' => $endDate,
            ],
        );
    }

    /**
     * Poll for settlement-report completion. Pass either the `request_id`
     * returned by `generate()`, or a `disbursement_id` from your dashboard.
     *
     * Endpoint: `GET /{version}/settlement/settlement-report/check-status`.
     *
     * @param  string|null  $requestId  Request id returned from `generate()`.
     * @param  string|null  $disbursementId  Disbursement id from the dashboard.
     * @return array<string, mixed>
     */
    public function checkStatus(?string $requestId = null, ?string $disbursementId = null): array
    {
        return $this->client->get(
            $this->path('settlement/settlement-report/check-status'),
            $this->compact(['request_id' => $requestId, 'disbursement_id' => $disbursementId]),
        );
    }
}
