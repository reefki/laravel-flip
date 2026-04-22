# Changelog

All notable changes to `reefki/laravel-flip` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.2.0] - 2026-04-23

### Added

- `ConnectionException` (extends `FlipException`) wrapping network/timeout failures so users only need to catch one exception family.
- `NotFoundException` test coverage.
- `Banks::find()` test for the `BANK_NOT_FOUND` path.
- Test for 19-digit BigInteger IDs returning as strings.
- `support.issues` and `support.source` URLs in `composer.json` (surface on Packagist).

### Changed

- Successful JSON responses are now decoded with `JSON_BIGINT_AS_STRING`. Flip's 19-digit transaction IDs that overflow PHP int64 are preserved as strings instead of silently truncated to floats.
- `Banks::find()` now returns `null` when Flip reports `BANK_NOT_FOUND` instead of throwing `ValidationException`. Previous behavior never matched the documented `?array` return type.
- Dynamic path segments (`Bill::find/update`, `Payment::forBill`, `InternationalDisbursement::find`) are URL-encoded with `rawurlencode()`.

### Removed

- `FlipServiceProvider::provides()` — was dead code (only consulted on `DeferrableProvider`, which the package isn't).

## [0.1.1] - 2026-04-23

### Added

- GitHub Actions test matrix (PHP 8.2/8.3/8.4 × Laravel 10/11/12).
- README badges (Packagist version, downloads, CI status, license).
- `CHANGELOG.md` following Keep a Changelog.

## [0.1.0] - 2026-04-23

### Added

- Initial release.
- `Balance`, `Banks`, `Maintenance` resources covering the General API.
- `Disbursement` resource: `create`, `list`, `find`, `findByIdempotencyKey`, plus v2-pinned `inquiry` (Bank Account Inquiry).
- `SpecialDisbursement` resource: `create` (PJP money transfer).
- `InternationalDisbursement` resource (v2-pinned): `exchangeRates`, `formData`, `find`, `list`, `createConsumer` (C2C/C2B), `createBusiness` (B2C/B2B).
- `Bill` resource (Accept Payment v3): `create`, `list`, `find`, `update`.
- `Payment` resource: `forBill`, `list`.
- `SettlementReport` resource: `generate`, `checkStatus`.
- `Reference` resource (v2-pinned): `cities`, `countries`, `citiesAndCountries`.
- Webhook signature validator (`Flip::webhook()->verify($request)`).
- Configurable default API version (`FLIP_VERSION`); per-resource override via `withVersion()`; global override via `Flip::useVersion()`.
- Typed exceptions: `AuthenticationException`, `NotFoundException`, `ValidationException`, `MaintenanceException`, `InvalidWebhookSignatureException` (all extend `FlipException`).
- 46 Pest tests, 70 assertions.

[Unreleased]: https://github.com/reefki/laravel-flip/compare/v0.2.0...HEAD
[0.2.0]: https://github.com/reefki/laravel-flip/compare/v0.1.1...v0.2.0
[0.1.1]: https://github.com/reefki/laravel-flip/compare/v0.1.0...v0.1.1
[0.1.0]: https://github.com/reefki/laravel-flip/releases/tag/v0.1.0
