<?php

use Illuminate\Support\Facades\Http;
use Reefki\Flip\Facades\Flip;

it('lists cities pinned to v2', function () {
    config()->set('flip.version', 'v3');
    Http::fake([flipUrl('v2/disbursement/city-list') => Http::response(['102' => 'Kab. Bekasi'], 200)]);

    expect(Flip::reference()->cities())->toBe(['102' => 'Kab. Bekasi']);
});

it('lists countries pinned to v2', function () {
    Http::fake([flipUrl('v2/disbursement/country-list') => Http::response(['100000' => 'Afghanistan'], 200)]);

    expect(Flip::reference()->countries())->toBe(['100000' => 'Afghanistan']);
});

it('lists combined city/country pinned to v2', function () {
    Http::fake([flipUrl('v2/disbursement/city-country-list') => Http::response(['102' => 'Kab. Bekasi'], 200)]);

    expect(Flip::reference()->citiesAndCountries())->toBe(['102' => 'Kab. Bekasi']);
});
