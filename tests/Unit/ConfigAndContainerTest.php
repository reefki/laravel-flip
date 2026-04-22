<?php

use Reefki\Flip\Client;
use Reefki\Flip\Facades\Flip;
use Reefki\Flip\Flip as FlipManager;

it('binds the flip manager as a singleton', function () {
    $a = app('flip');
    $b = app('flip');

    expect($a)->toBeInstanceOf(FlipManager::class)
        ->and($a)->toBe($b);
});

it('binds the client as a singleton', function () {
    $a = app(Client::class);
    $b = app(Client::class);

    expect($a)->toBeInstanceOf(Client::class)
        ->and($a)->toBe($b);
});

it('exposes the configured default version', function () {
    expect(app(Client::class)->defaultVersion())->toBe('v3');

    config()->set('flip.version', 'v2');
    app()->forgetInstance(Client::class);
    app()->forgetInstance('flip');

    expect(app(Client::class)->defaultVersion())->toBe('v2');
});

it('resolves the right base url per environment', function () {
    expect(app(Client::class)->baseUrl())->toBe('https://bigflip.id/big_sandbox_api');

    config()->set('flip.environment', 'production');
    app()->forgetInstance(Client::class);

    expect(app(Client::class)->baseUrl())->toBe('https://bigflip.id/api');
});

it('publishes the config file', function () {
    expect(file_exists(__DIR__ . '/../../config/flip.php'))->toBeTrue();
});

it('makes resources accessible via the facade', function () {
    expect(Flip::balance())->toBeInstanceOf(\Reefki\Flip\Resources\Balance::class)
        ->and(Flip::banks())->toBeInstanceOf(\Reefki\Flip\Resources\Banks::class)
        ->and(Flip::maintenance())->toBeInstanceOf(\Reefki\Flip\Resources\Maintenance::class)
        ->and(Flip::disbursement())->toBeInstanceOf(\Reefki\Flip\Resources\Disbursement::class)
        ->and(Flip::specialDisbursement())->toBeInstanceOf(\Reefki\Flip\Resources\SpecialDisbursement::class)
        ->and(Flip::internationalDisbursement())->toBeInstanceOf(\Reefki\Flip\Resources\InternationalDisbursement::class)
        ->and(Flip::bill())->toBeInstanceOf(\Reefki\Flip\Resources\Bill::class)
        ->and(Flip::payment())->toBeInstanceOf(\Reefki\Flip\Resources\Payment::class)
        ->and(Flip::settlementReport())->toBeInstanceOf(\Reefki\Flip\Resources\SettlementReport::class)
        ->and(Flip::reference())->toBeInstanceOf(\Reefki\Flip\Resources\Reference::class);
});
