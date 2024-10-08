<?php

namespace AnisAronno\LaravelAutoUpdater\Tests;

use AnisAronno\LaravelAutoUpdater\Console\Commands\CheckUpdateCommand;
use AnisAronno\LaravelAutoUpdater\Console\Commands\UpdateInitiateCommand;
use AnisAronno\LaravelAutoUpdater\Contracts\VCSProviderInterface;
use AnisAronno\LaravelAutoUpdater\LaravelAutoUpdaterServiceProvider;

class LaravelAutoUpdaterServiceProviderTest extends TestCase
{
    public function testServiceProviderIsLoaded()
    {
        $this->assertTrue(
            $this->app->providerIsLoaded(LaravelAutoUpdaterServiceProvider::class),
            'The LaravelAutoUpdaterServiceProvider is not loaded'
        );
    }

    public function testServiceProviderRegistersCommands()
    {
        $this->assertTrue($this->app->make(CheckUpdateCommand::class) !== null);
        $this->assertTrue($this->app->make(UpdateInitiateCommand::class) !== null);
    }

    public function testServiceProviderRegistersConfig()
    {
        $this->assertTrue($this->app->make('config')->has('auto-updater'));
    }

    public function testConfigurationIsPublished()
    {
        $this->artisan('vendor:publish', ['--provider' => LaravelAutoUpdaterServiceProvider::class])
            ->assertExitCode(0);

        $this->assertFileExists(config_path('auto-updater.php'));
    }

    public function testServiceProviderBindsVCSProviderInterface()
    {
        $this->app['config']->set('auto-updater.release_url', 'https://github.com/anisAronno/laravel-starter');

        $instance = $this->app->make(VCSProviderInterface::class);
        $this->assertInstanceOf(VCSProviderInterface::class, $instance);
    }

    public function testServiceProviderRegistersViews()
    {
        $this->assertArrayHasKey('auto-updater', $this->app['view']->getFinder()->getHints());
    }
}
