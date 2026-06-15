<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;
use Mantraideas\LaravelFonepay\LaravelFonepayServiceProvider;
use Mantraideas\LaravelFonepay\Services\FonepayService;

beforeEach(function () {
    $this->app = new Application;
    $this->app->instance('config', new Repository);
    Facade::setFacadeApplication($this->app);

    $this->provider = new LaravelFonepayServiceProvider($this->app);
});

test('service provider registers fonepay service as singleton', function () {
    $this->provider->register();

    expect($this->app->bound(FonepayService::class))->toBeTrue();
});

test('service provider returns same instance of fonepay service', function () {
    $this->provider->register();

    $instance1 = $this->app->make(FonepayService::class);
    $instance2 = $this->app->make(FonepayService::class);

    expect($instance1)->toBeInstanceOf(FonepayService::class);
    expect($instance2)->toBe($instance1);
});

test('service provider merges fonepay config', function () {
    $config = $this->app->make('config');

    expect($config->get('fonepay'))->toBeNull();

    $this->provider->register();

    expect($config->get('fonepay'))->toBeArray();
    expect($config->get('fonepay.username'))->toBe('labasam');
    expect($config->get('fonepay.password'))->toBe('F0nepay@123#');
    expect($config->get('fonepay.base_url'))->toBe('https://dev-external-gateway-new.fonepay.com/merchantThirdparty');
    expect($config->get('fonepay.base_path'))->toBe('/api/merchant/third-party/v2');
    expect($config->get('fonepay.terminal_id'))->toBe('4271423331147924');
});

test('service provider boot registers fonepay alias', function () {
    $this->provider->register();

    $this->provider->boot();

    $loader = AliasLoader::getInstance();
    $aliases = (fn () => $this->aliases)->call($loader);

    expect($aliases)->toHaveKey('Fonepay');
});
