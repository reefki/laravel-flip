<?php

use Illuminate\Support\Facades\Http;
use Reefki\Flip\Facades\Flip;

it('uses v3 by default', function () {
    Http::fake([flipUrl('v3/general/balance') => Http::response(['balance' => 1], 200)]);

    Flip::balance()->get();

    Http::assertSent(fn ($r) => str_contains($r->url(), '/v3/general/balance'));
});

it('lets you switch the default version via config', function () {
    config()->set('flip.version', 'v2');
    app()->forgetInstance(\Reefki\Flip\Client::class);
    app()->forgetInstance('flip');

    Http::fake([flipUrl('v2/general/balance') => Http::response(['balance' => 1], 200)]);

    Flip::balance()->get();

    Http::assertSent(fn ($r) => str_contains($r->url(), '/v2/general/balance'));
});

it('lets you override the version per resource via withVersion()', function () {
    Http::fake([flipUrl('v2/disbursement') => Http::response(['ok' => true], 200)]);

    Flip::disbursement()->withVersion('v2')->list();

    Http::assertSent(fn ($r) => str_contains($r->url(), '/v2/disbursement'));
});

it('lets you override the version globally via Flip::useVersion()', function () {
    Http::fake([flipUrl('v2/general/balance') => Http::response(['balance' => 1], 200)]);

    Flip::useVersion('v2')->balance()->get();

    Http::assertSent(fn ($r) => str_contains($r->url(), '/v2/general/balance'));
});

it('does not mutate the singleton when using useVersion()', function () {
    Http::fake([
        flipUrl('v2/general/balance') => Http::response(['balance' => 1], 200),
        flipUrl('v3/general/balance') => Http::response(['balance' => 2], 200),
    ]);

    Flip::useVersion('v2')->balance()->get();
    Flip::balance()->get();

    Http::assertSent(fn ($r) => str_contains($r->url(), '/v2/general/balance'));
    Http::assertSent(fn ($r) => str_contains($r->url(), '/v3/general/balance'));
});

it('pins accept-payment bills/payments/settlement-report to v3 even when default is v2', function () {
    config()->set('flip.version', 'v2');
    app()->forgetInstance(\Reefki\Flip\Client::class);
    app()->forgetInstance('flip');

    Http::fake([
        flipUrl('v3/pwf/bill') => Http::response(['link_id' => 1], 200),
        flipUrl('v3/pwf/abc/bill') => Http::response(['link_id' => 1], 200),
        flipUrl('v3/pwf/abc/payment*') => Http::response([], 200),
        flipUrl('v3/pwf/payment*') => Http::response([], 200),
        flipUrl('v3/settlement-report/generate') => Http::response(['request_id' => 'r'], 200),
        flipUrl('v3/settlement/settlement-report/check-status*') => Http::response(['status' => 'PROCESSING'], 200),
    ]);

    Flip::bill()->create(['title' => 'x', 'type' => 'SINGLE', 'expired_date' => '2026-01-01 00:00', 'step' => 'checkout']);
    Flip::bill()->list();
    Flip::bill()->find('abc');
    Flip::bill()->update('abc', ['status' => 'INACTIVE']);
    Flip::payment()->forBill('abc');
    Flip::payment()->list();
    Flip::settlementReport()->generate('2026-01-01', '2026-01-02');
    Flip::settlementReport()->checkStatus('r');

    Http::assertSentCount(8);
    Http::assertNotSent(fn ($r) => str_contains($r->url(), '/v2/pwf/'));
    Http::assertNotSent(fn ($r) => str_contains($r->url(), '/v2/settlement'));
});
