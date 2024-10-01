<?php

namespace AnisAronno\LaravelAutoUpdater\Tests;

use AnisAronno\LaravelAutoUpdater\Console\Commands\CheckUpdateCommand;
use AnisAronno\LaravelAutoUpdater\Services\ReleaseService;
use Illuminate\Support\Facades\Artisan;
use Mockery;
use Orchestra\Testbench\TestCase;

class CheckUpdateCommandTest extends TestCase
{
    protected $releaseService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->releaseService = Mockery::mock(ReleaseService::class);
        $this->app->instance(ReleaseService::class, $this->releaseService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return [\AnisAronno\LaravelAutoUpdater\LaravelAutoUpdaterServiceProvider::class];
    }

    public function testHandleWithUpdateAvailable()
    {
        $this->releaseService->shouldReceive('getCurrentVersion')->once()->andReturn('1.0.0');
        $this->releaseService->shouldReceive('collectReleaseData')->once()->andReturn([
            'version' => '1.1.0',
            'changelog' => 'Bug fixes and improvements',
        ]);

        $this->artisan('update:check')
            ->expectsOutput('🚀  Update Available! 🚀')
            ->expectsOutput('Current Version: 1.0.0')
            ->expectsOutput('Latest Version: 1.1.0')
            ->expectsOutput('Changelog: '.PHP_EOL.'Bug fixes and improvements')
            ->assertExitCode(0);
    }

    public function testHandleWithNoUpdateAvailable()
    {
        $this->releaseService->shouldReceive('getCurrentVersion')->once()->andReturn('1.1.0');
        $this->releaseService->shouldReceive('collectReleaseData')->once()->andReturn([
            'version' => '1.1.0',
            'changelog' => 'Bug fixes and improvements',
        ]);

        $this->artisan('update:check')
        ->expectsOutput('✅ Your project is up to date!')
        ->assertExitCode(0);
    }

    public function testHandleWithFailedToFetchReleaseData()
    {
        $this->releaseService->shouldReceive('getCurrentVersion')->once()->andReturn('1.0.0');
        $this->releaseService->shouldReceive('collectReleaseData')->once()->andReturn([]);

        $this->artisan('update:check')
            ->expectsOutput('Failed to fetch the latest release data.')
            ->assertExitCode(0);
    }

    public function testHandleWithException()
    {
        $this->releaseService->shouldReceive('getCurrentVersion')->once()->andThrow(new \Exception('Test exception'));

        $this->artisan('update:check')
            ->expectsOutput('An error occurred while checking for updates: Test exception')
            ->assertExitCode(1);
    }
}
