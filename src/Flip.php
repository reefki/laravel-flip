<?php

namespace Reefki\Flip;

use Reefki\Flip\Resources\Balance;
use Reefki\Flip\Resources\Banks;
use Reefki\Flip\Resources\Bill;
use Reefki\Flip\Resources\Disbursement;
use Reefki\Flip\Resources\InternationalDisbursement;
use Reefki\Flip\Resources\Maintenance;
use Reefki\Flip\Resources\Payment;
use Reefki\Flip\Resources\Reference;
use Reefki\Flip\Resources\Resource;
use Reefki\Flip\Resources\SettlementReport;
use Reefki\Flip\Resources\SpecialDisbursement;
use Reefki\Flip\Webhook\SignatureValidator;

class Flip
{
    /**
     * Underlying low-level HTTP client.
     *
     * @var \Reefki\Flip\Client
     */
    protected Client $client;

    /**
     * Default API version override applied to every resource created from
     * this instance. Falls through to the configured default when null.
     *
     * @var string|null
     */
    protected ?string $defaultVersionOverride = null;

    /**
     * Build a new Flip manager.
     *
     * @param  \Reefki\Flip\Client  $client  Low-level HTTP client.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Apply a default API version override for every resource created from
     * the returned instance. Returns a clone — the original is untouched.
     *
     * @param  string|null  $version  `v2` or `v3`. Pass `null` to clear the override.
     * @return static
     */
    public function useVersion(?string $version): static
    {
        $clone = clone $this;
        $clone->defaultVersionOverride = $version;

        return $clone;
    }

    /**
     * Access the underlying HTTP client for advanced usage.
     *
     * @return \Reefki\Flip\Client
     */
    public function client(): Client
    {
        return $this->client;
    }

    /**
     * Resource for the deposit balance endpoint.
     *
     * @return \Reefki\Flip\Resources\Balance
     */
    public function balance(): Balance
    {
        return $this->resource(Balance::class);
    }

    /**
     * Resource for the list-of-banks endpoint.
     *
     * @return \Reefki\Flip\Resources\Banks
     */
    public function banks(): Banks
    {
        return $this->resource(Banks::class);
    }

    /**
     * Resource for the maintenance-info endpoint.
     *
     * @return \Reefki\Flip\Resources\Maintenance
     */
    public function maintenance(): Maintenance
    {
        return $this->resource(Maintenance::class);
    }

    /**
     * Resource for money-transfer disbursement endpoints (and bank inquiry).
     *
     * @return \Reefki\Flip\Resources\Disbursement
     */
    public function disbursement(): Disbursement
    {
        return $this->resource(Disbursement::class);
    }

    /**
     * Resource for special money-transfer disbursement endpoints (PJP).
     *
     * @return \Reefki\Flip\Resources\SpecialDisbursement
     */
    public function specialDisbursement(): SpecialDisbursement
    {
        return $this->resource(SpecialDisbursement::class);
    }

    /**
     * Resource for international (cross-border) disbursement endpoints.
     *
     * @return \Reefki\Flip\Resources\InternationalDisbursement
     */
    public function internationalDisbursement(): InternationalDisbursement
    {
        return $this->resource(InternationalDisbursement::class);
    }

    /**
     * Resource for accept-payment bill / payment-link endpoints.
     *
     * @return \Reefki\Flip\Resources\Bill
     */
    public function bill(): Bill
    {
        return $this->resource(Bill::class);
    }

    /**
     * Resource for accept-payment payment listing endpoints.
     *
     * @return \Reefki\Flip\Resources\Payment
     */
    public function payment(): Payment
    {
        return $this->resource(Payment::class);
    }

    /**
     * Resource for the settlement-report endpoints.
     *
     * @return \Reefki\Flip\Resources\SettlementReport
     */
    public function settlementReport(): SettlementReport
    {
        return $this->resource(SettlementReport::class);
    }

    /**
     * Resource for read-only reference data (city, country lists).
     *
     * @return \Reefki\Flip\Resources\Reference
     */
    public function reference(): Reference
    {
        return $this->resource(Reference::class);
    }

    /**
     * Webhook signature validator for verifying incoming Flip callbacks.
     *
     * @return \Reefki\Flip\Webhook\SignatureValidator
     */
    public function webhook(): SignatureValidator
    {
        return new SignatureValidator((string) $this->client->validationToken());
    }

    /**
     * Instantiate a resource and apply the default version override, if set.
     *
     * @template T of \Reefki\Flip\Resources\Resource
     * @param  class-string<T>  $class  Fully-qualified resource class name.
     * @return T
     */
    protected function resource(string $class)
    {
        $resource = new $class($this->client);
        if ($this->defaultVersionOverride !== null) {
            $resource = $resource->withVersion($this->defaultVersionOverride);
        }

        return $resource;
    }
}
