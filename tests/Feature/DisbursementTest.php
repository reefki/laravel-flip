<?php

use Illuminate\Support\Facades\Http;
use Reefki\Flip\Facades\Flip;

/**
 * Sample response body taken straight from the docs. Wrapped in a function so
 * intelephense can statically follow it (Pest's $this->prop dynamic state
 * confuses static analyzers).
 *
 * @return array<string, mixed>
 */
function sampleDisbursement(): array
{
    return [
        'id' => 1234567890123456800,
        'user_id' => 20,
        'amount' => 10000,
        'status' => 'PENDING',
        'bank_code' => 'bni',
        'account_number' => '1122333300',
        'recipient_name' => 'John Doe',
        'idempotency_key' => 'idem-key-1',
    ];
}

it('creates a disbursement with idempotency key header', function () {
    Http::fake([flipUrl('v3/disbursement') => Http::response(sampleDisbursement(), 200)]);

    $result = Flip::disbursement()->create([
        'account_number' => '1122333300',
        'bank_code' => 'bni',
        'amount' => 10000,
        'remark' => 'Test',
    ], 'idem-key-1', '2026-04-23T12:00:00Z');

    expect($result['status'])->toBe('PENDING');

    Http::assertSent(function ($request) {
        return $request->method() === 'POST'
            && $request->url() === flipUrl('v3/disbursement')
            && $request->header('idempotency-key')[0] === 'idem-key-1'
            && $request->header('X-TIMESTAMP')[0] === '2026-04-23T12:00:00Z'
            && $request->header('Content-Type')[0] === 'application/x-www-form-urlencoded'
            && $request['account_number'] === '1122333300'
            && $request['bank_code'] === 'bni';
    });
});

it('omits null body fields', function () {
    Http::fake([flipUrl('v3/disbursement') => Http::response(sampleDisbursement(), 200)]);

    Flip::disbursement()->create([
        'account_number' => '1122333300',
        'bank_code' => 'bni',
        'amount' => 10000,
        'remark' => null,
    ], 'idem-key-1');

    Http::assertSent(fn ($r) => ! array_key_exists('remark', $r->data()));
});

it('lists disbursements with filters as query params', function () {
    Http::fake([flipUrl('v3/disbursement?pagination=10&page=2&status=DONE') => Http::response([], 200)]);

    Flip::disbursement()->list(['pagination' => 10, 'page' => 2, 'status' => 'DONE']);

    Http::assertSent(fn ($r) => $r->method() === 'GET'
        && str_contains($r->url(), 'pagination=10')
        && str_contains($r->url(), 'status=DONE'));
});

it('finds disbursement by id', function () {
    Http::fake([flipUrl('v3/get-disbursement?id=42') => Http::response(sampleDisbursement(), 200)]);

    $result = Flip::disbursement()->find(42);
    expect($result['id'])->toBe(1234567890123456800);
});

it('finds disbursement by idempotency key', function () {
    Http::fake([flipUrl('v3/get-disbursement?idempotency-key=idem-key-1') => Http::response(sampleDisbursement(), 200)]);

    Flip::disbursement()->findByIdempotencyKey('idem-key-1');

    Http::assertSent(fn ($r) => str_contains($r->url(), 'idempotency-key=idem-key-1'));
});

it('runs bank account inquiry pinned to v2', function () {
    Http::fake([
        flipUrl('v2/disbursement/bank-account-inquiry') => Http::response([
            'bank_code' => 'bca',
            'account_number' => '5465327020',
            'account_holder' => 'PT Fliptech Lentera IP',
            'status' => 'SUCCESS',
            'inquiry_key' => 'aVncCDdKW9dciRvH9qSH',
            'is_virtual_account' => false,
        ], 200),
    ]);

    $result = Flip::disbursement()->inquiry('5465327020', 'bca', 'aVncCDdKW9dciRvH9qSH');

    expect($result['account_holder'])->toBe('PT Fliptech Lentera IP');

    Http::assertSent(fn ($r) => $r->method() === 'POST'
        && $r->url() === flipUrl('v2/disbursement/bank-account-inquiry'));
});
