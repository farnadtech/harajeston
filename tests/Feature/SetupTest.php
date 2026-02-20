<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('environment is configured correctly', function () {
    expect(config('app.locale'))->toBe('fa')
        ->and(config('app.timezone'))->toBe('Asia/Tehran');
});
