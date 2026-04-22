<?php

use Illuminate\Support\Facades\Http;
use Reefki\Flip\Facades\Flip;

it('fetches exchange rates pinned to v2 even if default is v3', function () {
    config()->set('flip.version', 'v3');
    Http::fake([flipUrl('v2/international-disbursement/exchange-rates*') => Http::response([], 200)]);

    Flip::internationalDisbursement()->exchangeRates('C2C', 'USA');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/v2/international-disbursement/exchange-rates')
        && str_contains($r->url(), 'transaction_type=C2C')
        && str_contains($r->url(), 'country_iso_code=USA'));
});

it('fetches form data', function () {
    Http::fake([flipUrl('v2/international-disbursement/form-data*') => Http::response([], 200)]);

    Flip::internationalDisbursement()->formData('B2B');

    Http::assertSent(fn ($r) => str_contains($r->url(), '/v2/international-disbursement/form-data'));
});

it('finds and lists international transfers', function () {
    Http::fake([
        flipUrl('v2/international-disbursement/abc123') => Http::response(['id' => 'abc123'], 200),
        flipUrl('v2/international-disbursement*') => Http::response([], 200),
    ]);

    Flip::internationalDisbursement()->find('abc123');
    Flip::internationalDisbursement()->list(['pagination' => 5]);

    Http::assertSentCount(2);
    Http::assertSent(fn ($r) => $r->url() === flipUrl('v2/international-disbursement/abc123'));
    Http::assertSent(fn ($r) => str_contains($r->url(), '/v2/international-disbursement?pagination=5'));
});

it('creates C2C and B2B transfers with idempotency key', function () {
    Http::fake([
        flipUrl('v2/international-disbursement') => Http::response(['id' => 1], 200),
        flipUrl('v2/international-disbursement/create-with-attachment') => Http::response(['id' => 2], 200),
    ]);

    Flip::internationalDisbursement()->createConsumer(['amount' => '100'], 'idem-c2c');
    Flip::internationalDisbursement()->createBusiness(['destination_country' => 'USA'], 'idem-b2b');

    Http::assertSent(fn ($r) => $r->url() === flipUrl('v2/international-disbursement')
        && $r->method() === 'POST'
        && $r->header('idempotency-key')[0] === 'idem-c2c');

    Http::assertSent(fn ($r) => $r->url() === flipUrl('v2/international-disbursement/create-with-attachment')
        && $r->header('idempotency-key')[0] === 'idem-b2b');
});
