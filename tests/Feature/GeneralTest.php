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

it('checks maintenance status', function () {
    Http::fake([
        flipUrl('v3/general/maintenance') => Http::response(['maintenance' => false], 200),
    ]);

    expect(Flip::maintenance()->isUnderMaintenance())->toBeFalse();
});
