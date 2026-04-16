<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\SupplierServiceInterface;
use App\Services\SupplierService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SupplierServiceInterface::class, SupplierService::class);
    }

    public function boot(): void
    {
        //
    }
}