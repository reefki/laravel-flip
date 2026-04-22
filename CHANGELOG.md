# Changelog

All notable changes to `reefki/laravel-flip` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

[Unreleased]: https://github.com/reefki/laravel-flip/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/reefki/laravel-flip/releases/tag/v0.1.0
