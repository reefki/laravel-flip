<?php

namespace Reefki\Flip\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Reefki\Flip\FlipServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [FlipServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('flip', [
            'secret_key' => 'test-secret-key',
            'environment' => 'sandbox',
            'version' => 'v3',
            'base_urls' => [
                'production' => 'https://bigflip.id/api',
                'sandbox' => 'https://bigflip.id/big_sandbox_api',
            ],
            'validation_token' => 'test-validation-token',
            'http' => [
                'timeout' => 5,
                'connect_timeout' => 5,
                'retry_times' => 0,
                'retry_sleep_ms' => 100,
            ],
        ]);
    }
}
