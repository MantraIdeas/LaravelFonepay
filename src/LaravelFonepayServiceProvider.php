<?php

declare(strict_types=1);

namespace Mantraideas\LaravelFonepay;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Mantraideas\LaravelFonepay\Facades\Fonepay as FonepayFacade;
use Mantraideas\LaravelFonepay\Services\FonepayService;

class LaravelFonepayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/fonepay.php', 'fonepay');

        $this->app->singleton(FonepayService::class);
    }

    public function boot(): void
    {
        AliasLoader::getInstance()->alias('Fonepay', FonepayFacade::class);

        $this->publishes([
            __DIR__.'/config/fonepay.php' => config_path('fonepay.php'),
        ], 'fonepay-config');
    }
}
