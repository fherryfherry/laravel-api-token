<?php
namespace LaravelApiToken;

use Illuminate\Support\ServiceProvider;

class LaravelSimpleApiTokenServiceProvider extends ServiceProvider
{

    public function boot() {
        if($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/Migrations/create_laravel_api_tokens.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_laravel_api_tokens.php')
            ], 'laravel_api_token_migration');
            $this->publishes([
                __DIR__.'/Config/laravel_simple_api_token.php' => config_path('laravel_simple_api_token.php'),
            ], 'laravel_api_token_config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/Config/laravel_simple_api_token.php', 'laravel_simple_api_token');
    }

}