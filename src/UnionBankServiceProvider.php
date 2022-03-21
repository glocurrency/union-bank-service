<?php

namespace GloCurrency\UnionBank;

use Illuminate\Support\ServiceProvider;
use GloCurrency\UnionBank\Console\FetchTransactionsUpdateCommand;
use GloCurrency\UnionBank\Config;
use BrokeYourBike\UnionBank\Interfaces\ConfigInterface;

class UnionBankServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMigrations();
        $this->registerPublishing();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configure();
        $this->bindConfig();
        $this->registerCommands();
    }

    /**
     * Setup the configuration for UnionBank.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/union_bank.php', 'services.union_bank'
        );
    }

    /**
     * Bind the UnionBank config interface to the UnionBank config.
     *
     * @return void
     */
    protected function bindConfig()
    {
        $this->app->bind(ConfigInterface::class, Config::class);
    }

    /**
     * Register the package migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if (UnionBank::$runsMigrations && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/union_bank.php' => $this->app->configPath('union_bank.php'),
            ], 'union-bank-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
            ], 'union-bank-migrations');
        }
    }

    /**
     * Register the package's commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FetchTransactionsUpdateCommand::class,
            ]);
        }
    }
}
