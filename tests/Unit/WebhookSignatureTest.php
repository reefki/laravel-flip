<?php

use Illuminate\Http\Request;
use Reefki\Flip\Exceptions\InvalidWebhookSignatureException;
use Reefki\Flip\Facades\Flip;

it('accepts a callback whose token matches the configured value', function () {
    $validator = Flip::webhook();

    expect($validator->isValid('test-validation-token'))->toBeTrue()
        ->and($validator->isValid('wrong'))->toBeFalse()
        ->and($validator->isValid(null))->toBeFalse()
        ->and($validator->isValid(''))->toBeFalse();
});

it('verifies a request and returns the decoded data payload', function () {
    $payload = ['id' => '1234567890123456789', 'status' => 'DONE', 'amount' => '10000'];
    $request = Request::create('/callback', 'POST', [
        'token' => 'test-validation-token',
        'data' => json_encode($payload),
    ]);

    expect(Flip::webhook()->verify($request))->toEqual($payload);
});

it('throws when the request token is wrong', function () {
    $request = Request::create('/callback', 'POST', [
        'token' => 'nope',
        'data' => json_encode(['x' => 1]),
    ]);

    Flip::webhook()->verify($request);
})->throws(InvalidWebhookSignatureException::class);

it('throws when data is not valid JSON', function () {
    $request = Request::create('/callback', 'POST', [
        'token' => 'test-validation-token',
        'data' => 'not-json',
    ]);

    Flip::webhook()->verify($request);
})->throws(InvalidWebhookSignatureException::class);

it('rejects callbacks when no validation token is configured', function () {
    config()->set('flip.validation_token', '');
    app()->forgetInstance('flip');
    app()->forgetInstance(\Reefki\Flip\Client::class);

    expect(app('flip')->webhook()->isValid('anything'))->toBeFalse();
});
