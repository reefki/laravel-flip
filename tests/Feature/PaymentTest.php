<?php

use Illuminate\Support\Facades\Http;
use Reefki\Flip\Facades\Flip;

it('lists payments for a bill', function () {
    Http::fake([flipUrl('v3/pwf/abc/payment*') => Http::response([], 200)]);

    Flip::payment()->forBill('abc', ['start_date' => '2026-01-01', 'end_date' => '2026-12-31']);

    Http::assertSent(fn ($r) => str_contains($r->url(), '/v3/pwf/abc/payment')
        && str_contains($r->url(), 'start_date=2026-01-01'));
});

it('lists all payments', function () {
    Http::fake([flipUrl('v3/pwf/payment*') => Http::response([], 200)]);

    Flip::payment()->list(['reference_id' => 'MerchantRef-1']);

    Http::assertSent(fn ($r) => str_contains($r->url(), '/v3/pwf/payment')
        && str_contains($r->url(), 'reference_id=MerchantRef-1'));
});
