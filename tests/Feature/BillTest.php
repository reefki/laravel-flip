<?php

use Illuminate\Support\Facades\Http;
use Reefki\Flip\Facades\Flip;

it('creates a bill as JSON', function () {
    Http::fake([flipUrl('v3/pwf/bill') => Http::response(['link_id' => 2502091430251230000], 200)]);

    Flip::bill()->create([
        'title' => 'Coffee Table',
        'type' => 'SINGLE',
        'amount' => 30000,
        'expired_date' => '2026-12-30 15:50',
        'step' => 'checkout',
    ]);

    Http::assertSent(fn ($r) => $r->method() === 'POST'
        && $r->url() === flipUrl('v3/pwf/bill')
        && str_starts_with($r->header('Content-Type')[0], 'application/json')
        && $r['title'] === 'Coffee Table');
});

it('lists bills', function () {
    Http::fake([flipUrl('v3/pwf/bill') => Http::response([], 200)]);

    Flip::bill()->list();

    Http::assertSent(fn ($r) => $r->method() === 'GET' && $r->url() === flipUrl('v3/pwf/bill'));
});

it('finds a bill by id', function () {
    Http::fake([flipUrl('v3/pwf/abc123/bill') => Http::response(['link_id' => 1], 200)]);

    Flip::bill()->find('abc123');

    Http::assertSent(fn ($r) => $r->url() === flipUrl('v3/pwf/abc123/bill'));
});

it('updates a bill via PUT', function () {
    Http::fake([flipUrl('v3/pwf/abc123/bill') => Http::response(['link_id' => 1], 200)]);

    Flip::bill()->update('abc123', ['status' => 'INACTIVE']);

    Http::assertSent(fn ($r) => $r->method() === 'PUT'
        && $r->url() === flipUrl('v3/pwf/abc123/bill')
        && $r['status'] === 'INACTIVE');
});
