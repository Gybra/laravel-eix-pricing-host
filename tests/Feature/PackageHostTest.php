<?php

declare(strict_types=1);

use Gybra\EixPricing\EixPricingServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;

it('boots the package migrations, command, and API route', function (): void {
    expect(app()->getProvider(EixPricingServiceProvider::class))->not->toBeNull();

    $this->artisan('migrate')->assertSuccessful();

    expect(Schema::hasTable('eix_imports'))->toBeTrue()
        ->and(Schema::hasTable('eix_quotes'))->toBeTrue()
        ->and(Artisan::all())->toHaveKey('eix:import')
        ->and(config('filesystems.disks.s3.driver'))->toBe('s3')
        ->and(class_exists(AwsS3V3Adapter::class))->toBeTrue();

    $this->getJson('/api/quotes/not-an-isin')->assertUnprocessable();
});
