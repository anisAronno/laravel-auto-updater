<?php

namespace AnisAronno\LaravelAutoUpdater\Console\Commands;

use Illuminate\Console\Command;
use AnisAronno\LaravelAutoUpdater\Services\VersionService;

class CheckUpdateCommand extends Command
{
    protected $signature = 'update:check';

    protected $description = 'Check for available updates for the project';

    protected VersionService $versionService;

    public function __construct(VersionService $versionService)
    {
        parent::__construct();
        $this->versionService = $versionService;
    }

    public function handle()
    {
        try {
            // Get the current version and latest release data
            $currentVersion = $this->versionService->getCurrentVersion();
            $latestRelease = $this->versionService->collectReleaseData();

            if (empty($latestRelease)) {
                $this->alert('No update data found.');
                return Command::SUCCESS;
            }

            $latestVersion = !empty($latestRelease['version']) ? ltrim($latestRelease['version'], 'v') : 'Not found';
            $changelog = $latestRelease['changelog'] ?? 'No changelog available';

            // Compare the current version with the latest release
            if (version_compare($latestVersion, $currentVersion, '>')) {
                $this->alert('Update Available!');
                $this->line('Current Version: ' . $currentVersion);
                $this->line('Latest Version: ' . $latestVersion);
                $this->line('Changelog: ' . PHP_EOL . $changelog);
            } else {
                $this->info('Your project is already up to date.');
            }

            return Command::SUCCESS;
        } catch (\Throwable $th) {
            $this->error('An error occurred while checking for updates: ' . $th->getMessage());
            return Command::FAILURE;
        }
    }
}
