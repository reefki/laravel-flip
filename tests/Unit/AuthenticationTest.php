<?php

use Illuminate\Support\Facades\Http;
use Reefki\Flip\Facades\Flip;

it('sends basic auth using the secret key as the username', function () {
    Http::fake([
        flipUrl('v3/general/balance') => Http::response(['balance' => 100], 200),
    ]);

    Flip::balance()->get();

    Http::assertSent(function ($request) {
        // Secret key is "test-secret-key"; basic auth value = base64("test-secret-key:")
        $expected = 'Basic ' . base64_encode('test-secret-key:');

        return $request->header('Authorization')[0] === $expected;
    });
});

it('throws AuthenticationException on 401', function () {
    Http::fake([
        flipUrl('v3/general/balance') => Http::response([
            'name' => 'Unauthorized',
            'message' => 'Your request was made with invalid credentials.',
            'status' => 401,
        ], 401),
    ]);

    Flip::balance()->get();
})->throws(\Reefki\Flip\Exceptions\AuthenticationException::class);

it('throws ValidationException on 422 and exposes Flip errors', function () {
    Http::fake([
        flipUrl('v3/disbursement') => Http::response([
            'code' => 'VALIDATION_ERROR',
            'errors' => [
                ['attribute' => 'amount', 'code' => 1001, 'message' => 'Amount cannot be empty'],
            ],
        ], 422),
    ]);

    try {
        Flip::disbursement()->create(
            ['account_number' => '1', 'bank_code' => 'bca', 'amount' => '0'],
            'idem-1',
        );
        $this->fail('Expected ValidationException');
    } catch (\Reefki\Flip\Exceptions\ValidationException $e) {
        expect($e->errors())->toHaveCount(1)
            ->and($e->errors()[0]['attribute'])->toBe('amount');
    }
});

it('throws MaintenanceException on 503', function () {
    Http::fake([
        flipUrl('v3/general/balance') => Http::response([
            'name' => 'SERVICE_UNAVAILABLE',
            'message' => 'Service or server is under maintenance',
            'status' => 503,
        ], 503),
    ]);

    Flip::balance()->get();
})->throws(\Reefki\Flip\Exceptions\MaintenanceException::class);
