<?php

use Illuminate\Support\Facades\Http;
use Reefki\Flip\Facades\Flip;

it('generates a settlement report', function () {
    Http::fake([flipUrl('v3/settlement-report/generate') => Http::response(['request_id' => 'req-1'], 200)]);

    Flip::settlementReport()->generate('2026-01-01', '2026-01-09');

    Http::assertSent(fn ($r) => $r->method() === 'POST'
        && $r->url() === flipUrl('v3/settlement-report/generate')
        && $r['settlement_date_start'] === '2026-01-01'
        && $r['settlement_date_end'] === '2026-01-09');
});

it('checks settlement report status by request id', function () {
    Http::fake([flipUrl('v3/settlement/settlement-report/check-status*') => Http::response(['status' => 'PROCESSING'], 200)]);

    Flip::settlementReport()->checkStatus('req-1');

    Http::assertSent(fn ($r) => str_contains($r->url(), 'request_id=req-1'));
});
