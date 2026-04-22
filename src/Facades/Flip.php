<?php

namespace Reefki\Flip\Facades;

use Illuminate\Support\Facades\Facade;
use Reefki\Flip\Client;
use Reefki\Flip\Resources\Balance;
use Reefki\Flip\Resources\Banks;
use Reefki\Flip\Resources\Bill;
use Reefki\Flip\Resources\Disbursement;
use Reefki\Flip\Resources\InternationalDisbursement;
use Reefki\Flip\Resources\Maintenance;
use Reefki\Flip\Resources\Payment;
use Reefki\Flip\Resources\Reference;
use Reefki\Flip\Resources\SettlementReport;
use Reefki\Flip\Resources\SpecialDisbursement;
use Reefki\Flip\Webhook\SignatureValidator;

/**
 * @method static \Reefki\Flip\Flip useVersion(?string $version)
 * @method static Client client()
 * @method static Balance balance()
 * @method static Banks banks()
 * @method static Maintenance maintenance()
 * @method static Disbursement disbursement()
 * @method static SpecialDisbursement specialDisbursement()
 * @method static InternationalDisbursement internationalDisbursement()
 * @method static Bill bill()
 * @method static Payment payment()
 * @method static SettlementReport settlementReport()
 * @method static Reference reference()
 * @method static SignatureValidator webhook()
 *
 * @see \Reefki\Flip\Flip
 */
class Flip extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'flip';
    }
}
