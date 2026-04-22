<?php

use Illuminate\Support\Facades\Http;
use Reefki\Flip\Facades\Flip;

it('fetches the deposit balance', function () {
    Http::fake([
        flipUrl('v3/general/balance') => Http::response(['balance' => 49656053], 200),
    ]);

    expect(Flip::balance()->get())->toBe(['balance' => 49656053]);
});

it('lists all banks', function () {
    Http::fake([
        flipUrl('v3/general/banks') => Http::response([
            ['bank_code' => 'mandiri', 'name' => 'Mandiri', 'fee' => 5000, 'queue' => 8, 'status' => 'OPERATIONAL'],
            ['bank_code' => 'bca', 'name' => 'BCA', 'fee' => 4000, 'queue' => 6, 'status' => 'OPERATIONAL'],
        ], 200),
    ]);

    $banks = Flip::banks()->list();
    expect($banks)->toHaveCount(2)
        ->and($banks[0]['bank_code'])->toBe('mandiri');
});

it('filters bank list by code', function () {
    Http::fake([
        flipUrl('v3/general/banks?code=bca') => Http::response([
            ['bank_code' => 'bca', 'name' => 'BCA', 'fee' => 4000, 'queue' => 6, 'status' => 'OPERATIONAL'],
        ], 200),
    ]);

    $bank = Flip::banks()->find('bca');
    expect($bank['bank_code'])->toBe('bca');
});

it('returns null from Banks::find when Flip says BANK_NOT_FOUND', function () {
    Http::fake([
        flipUrl('v3/general/banks?code=nope') => Http::response(['message' => 'BANK_NOT_FOUND'], 422),
    ]);

    expect(Flip::banks()->find('nope'))->toBeNull();
});

it('preserves 19-digit BigInteger ids as strings via JSON_BIGINT_AS_STRING', function () {
    Http::fake([
        flipUrl('v3/get-disbursement?id=99') => Http::response(
            // 20-digit value that overflows int64 — must arrive as a string.
            '{"id": 99999999999999999999, "status": "DONE"}',
            200,
            ['Content-Type' => 'application/json'],
        ),
    ]);

    $result = Flip::disbursement()->find(99);

    expect($result['id'])->toBeString()->toBe('99999999999999999999');
});

it('checks maintenance status', function () {
    Http::fake([
        flipUrl('v3/general/maintenance') => Http::response(['maintenance' => false], 200),
    ]);

    expect(Flip::maintenance()->isUnderMaintenance())->toBeFalse();
});
