<?php
/**
 * @package     IdnPlay\Laravel\Utils\Providers - ServiceProvider
 * @author      singkek
 * @copyright   Copyright(c) 2019
 * @version     1
 * @created     2020-01-24
 * @updated     2020-01-24
 **/

namespace IdnPlay\Laravel\Utils\Providers;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
use Illuminate\Contracts\Container\Container as Application;
use Illuminate\Foundation\Application as LaravelApplication;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $this->setupConfig($this->app);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
    }

    /**
     * Setup the config.
     *
     * @param \Illuminate\Contracts\Container\Container $app
     */
    protected function setupConfig(Application $app)
    {
        $source = __DIR__ . '/../Config/config.php';

        if ($app instanceof LaravelApplication && $app->runningInConsole()) {
            $this->publishes([$source => config_path('idnplay.php')], 'idnplay-config');
        } elseif ($app instanceof LumenApplication) {
            $app->configure('idnplay');
        }

        $this->mergeConfigFrom($source, 'idnplay');
    }
}
