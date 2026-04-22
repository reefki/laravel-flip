<?php

use Reefki\Flip\Tests\TestCase;

uses(TestCase::class)->in('Unit', 'Feature');

/**
 * Build a Flip-style sandbox URL for the given path. Keeps test setup terse.
 */
function flipUrl(string $path): string
{
    return 'https://bigflip.id/big_sandbox_api/' . ltrim($path, '/');
}
