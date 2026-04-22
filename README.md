# Laravel Flip

A fluent, fully-tested Laravel SDK for the [Flip for Business](https://docs.flip.id/docs/api/flip-for-business-api-documentation) payment API.

Covers everything Flip publishes:

- **General** — balance, supported banks, maintenance probe
- **Disbursement** (v2 + v3) — money transfer, list, lookup by id / idempotency key, bank account inquiry, special money transfer (PJP)
- **Reference data** — city, country, combined lists
- **Accept Payment** (v3) — create / list / read / edit bill, list payments per bill, list all payments, settlement report
- **International Disbursement** (v2) — exchange rates, form data, list, find, C2C/C2B + B2C/B2B create
- **Webhook signature validation**

Both v2 and v3 are first-class. Pick the default in config, override per call.

## Install

```bash
composer require reefki/laravel-flip
```

The service provider and `Flip` facade are auto-discovered.

Publish the config (optional):

```bash
php artisan vendor:publish --tag=flip-config
```

## Configure

Set these in your `.env`:

```env
FLIP_SECRET_KEY=your-secret-key
FLIP_VALIDATION_TOKEN=your-callback-validation-token
FLIP_ENVIRONMENT=sandbox      # or production
FLIP_VERSION=v3               # default API version: v2 or v3
```

`config/flip.php` exposes everything (timeout, retries, base URLs).

> The default version applies to endpoints that exist on both v2 and v3 (disbursement, accept-payment). A few endpoints are pinned to a specific version because Flip only ships them there: bank account inquiry, city/country lists, exchange rates, and the international transfer family are v2-only. Those resources ignore the default — see "Versioning" below.

## Quickstart

```php
use Reefki\Flip\Facades\Flip;

// Always check the deposit balance first
$balance = Flip::balance()->get();              // ['balance' => 49656053]

// Find a recipient bank
$bca = Flip::banks()->find('bca');              // operational status, fee, queue

// Verify the account holder name (cached → sync; uncached → callback)
$inquiry = Flip::disbursement()->inquiry('5465327020', 'bca');

// Create a disbursement (idempotency key is required by Flip)
$tx = Flip::disbursement()->create(
    payload: [
        'account_number' => '1122333300',
        'bank_code'      => 'bni',
        'amount'         => 10000,
        'remark'         => 'Salary - April',
    ],
    idempotencyKey: 'salary-april-user-123',
);
```

## Resources

### Balance

```php
Flip::balance()->get();          // ['balance' => int]
```

### Banks

```php
Flip::banks()->list();           // all banks
Flip::banks()->list('bca');      // filter by code
Flip::banks()->find('bca');      // single entry, or null
```

### Maintenance

```php
Flip::maintenance()->check();              // ['maintenance' => bool]
Flip::maintenance()->isUnderMaintenance(); // bool
```

### Disbursement (Money Transfer)

```php
$tx = Flip::disbursement()->create(
    payload: [
        'account_number'   => '1122333300',
        'bank_code'        => 'bni',
        'amount'           => 10000,
        'remark'           => 'Refund',
        'recipient_city'   => 391,
        'beneficiary_email'=> 'user@example.com',
    ],
    idempotencyKey: 'refund-order-987',
    timestamp: now()->toIso8601String(), // optional X-TIMESTAMP
);

Flip::disbursement()->list(['pagination' => 50, 'page' => 2, 'status' => 'DONE']);
Flip::disbursement()->find('1234567890123456800');
Flip::disbursement()->findByIdempotencyKey('refund-order-987');
```

### Bank Account Inquiry

`inquiry()` is pinned to `v2` — Flip has not shipped a v3 of this endpoint.

```php
$inquiry = Flip::disbursement()->inquiry(
    accountNumber: '5465327020',
    bankCode: 'bca',
    inquiryKey: 'order-987',  // optional, for matching async callbacks
);
```

If the result isn't cached, `status` will be `PENDING` and Flip will hit your configured callback URL with the final result.

### Special Money Transfer (PJP)

```php
Flip::specialDisbursement()->create(
    payload: [
        'account_number' => '1122333300',
        'bank_code'      => 'bni',
        'amount'         => 10000,
        'sender_name'    => 'John Doe',
        'sender_address' => 'Jl. Example 123',
        'sender_country' => 100252,
        'sender_job'     => 'entrepreneur',
        'direction'      => 'DOMESTIC_SPECIAL_TRANSFER',
    ],
    idempotencyKey: 'special-1',
);
```

### Accept Payment (Bills)

```php
$bill = Flip::bill()->create([
    'title'        => 'Coffee Table',
    'type'         => 'SINGLE',
    'amount'       => 30000,
    'expired_date' => '2026-12-30 15:50',
    'step'         => 'checkout',          // checkout | checkout_seamless | direct_api
    'reference_id' => 'order-1234',
]);

Flip::bill()->list();
Flip::bill()->find($billId);
Flip::bill()->update($billId, ['status' => 'INACTIVE']);
```

### Payments

```php
Flip::payment()->forBill($billId, ['start_date' => '2026-01-01']);
Flip::payment()->list(['reference_id' => 'order-1234']);
```

### Settlement Report

```php
$report = Flip::settlementReport()->generate('2026-01-01', '2026-01-09');
$status = Flip::settlementReport()->checkStatus($report['request_id']);
```

### International Disbursement (v2)

```php
$rates = Flip::internationalDisbursement()->exchangeRates('C2C', 'USA');
$form  = Flip::internationalDisbursement()->formData('C2C', 'USA');

Flip::internationalDisbursement()->createConsumer($payload, 'idem-c2c-1');
Flip::internationalDisbursement()->createBusiness($payload, 'idem-b2b-1');

Flip::internationalDisbursement()->find($transactionId);
Flip::internationalDisbursement()->list(['pagination' => 25]);
```

### Reference Data

```php
Flip::reference()->cities();              // ['102' => 'Kab. Bekasi', ...]
Flip::reference()->countries();           // ['100000' => 'Afghanistan', ...]
Flip::reference()->citiesAndCountries();  // both, merged
```

## Versioning

Flip ships some endpoints on both v2 and v3, others on only one. The package handles that for you:

- **Configurable default**: `config('flip.version')` (default `v3`) applies to all multi-version resources.
- **Per-call override**: `Flip::disbursement()->withVersion('v2')->list()` returns a clone with the version forced.
- **Global override**: `Flip::useVersion('v2')->disbursement()->list()` for a one-off facade chain.
- **Pinned endpoints**: bank account inquiry, city/country lists, exchange rates, form data, and every international-disbursement endpoint are pinned to v2 because Flip does not publish v3 versions. They ignore the default and the override.

```php
// Force a v2 disbursement create even if FLIP_VERSION=v3
Flip::disbursement()->withVersion('v2')->create($payload, 'idem-1');

// Or for an entire facade chain
Flip::useVersion('v2')->disbursement()->list();
```

## Webhooks

Every Flip callback POSTs `application/x-www-form-urlencoded` with two fields: `data` (JSON-encoded payload) and `token` (your validation token). Verify it with one call:

```php
use Illuminate\Http\Request;
use Reefki\Flip\Facades\Flip;
use Reefki\Flip\Exceptions\InvalidWebhookSignatureException;

Route::post('/webhooks/flip/disbursement', function (Request $request) {
    try {
        $payload = Flip::webhook()->verify($request);
    } catch (InvalidWebhookSignatureException) {
        abort(403);
    }

    // $payload is the decoded `data` array
    // ['id' => '1234567890123456789', 'status' => 'DONE', 'amount' => '10000', ...]

    Disbursement::where('flip_id', $payload['id'])->update([
        'status'      => $payload['status'],
        'reason'      => $payload['reason'] ?? null,
        'time_served' => $payload['time_served'] ?? null,
    ]);

    return response()->noContent();   // Flip retries non-200s 5x at 2-min intervals
});
```

Or just check whether a token is valid:

```php
if (Flip::webhook()->isValid($request->input('token'))) {
    // ...
}
```

## Errors

Flip's documented error responses map to typed exceptions:

| HTTP | Exception                                              |
|------|--------------------------------------------------------|
| 401  | `Reefki\Flip\Exceptions\AuthenticationException`       |
| 404  | `Reefki\Flip\Exceptions\NotFoundException`             |
| 422  | `Reefki\Flip\Exceptions\ValidationException`           |
| 503  | `Reefki\Flip\Exceptions\MaintenanceException`          |
| ★    | `Reefki\Flip\Exceptions\FlipException` (base class)    |

All inherit from `FlipException`, which exposes the response body and Flip's `errors[]` array:

```php
use Reefki\Flip\Exceptions\ValidationException;

try {
    Flip::disbursement()->create($payload, 'idem-1');
} catch (ValidationException $e) {
    foreach ($e->errors() as $err) {
        logger()->warning('flip.validation', $err); // ['attribute' => ..., 'code' => ..., 'message' => ...]
    }
    throw $e;
}
```

## Testing your code

The package uses Laravel's HTTP client under the hood, so you can fake requests in your own tests with `Http::fake()`:

```php
use Illuminate\Support\Facades\Http;
use Reefki\Flip\Facades\Flip;

Http::fake([
    'bigflip.id/big_sandbox_api/v3/disbursement' => Http::response([
        'id' => 1234567890123456800,
        'status' => 'PENDING',
    ], 200),
]);

$result = Flip::disbursement()->create($payload, 'idem-1');
```

## Running the package's own test suite

```bash
composer install
vendor/bin/pest
```

46 tests covering every resource, both versions, error mapping, and webhook signature validation.

## License

MIT.
