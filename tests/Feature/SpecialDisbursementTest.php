<?php

use Illuminate\Support\Facades\Http;
use Reefki\Flip\Facades\Flip;

it('creates a special money transfer with sender details', function () {
    Http::fake([flipUrl('v3/special-disbursement') => Http::response(['id' => 1, 'status' => 'PENDING'], 200)]);

    Flip::specialDisbursement()->create([
        'account_number' => '1122333300',
        'bank_code' => 'bni',
        'amount' => 10000,
        'sender_name' => 'John Doe',
        'sender_address' => 'Some Address',
        'sender_country' => 100252,
        'sender_job' => 'entrepreneur',
        'direction' => 'DOMESTIC_SPECIAL_TRANSFER',
    ], 'idem-special-1');

    Http::assertSent(fn ($r) => $r->method() === 'POST'
        && $r->url() === flipUrl('v3/special-disbursement')
        && $r->header('idempotency-key')[0] === 'idem-special-1'
        && $r['sender_name'] === 'John Doe');
});
