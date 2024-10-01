<?php

namespace AnisAronno\LaravelAutoUpdater\Services;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

/**
 * Class DownloadService
 * 
 * This class provides download-related operations.
 */
class DownloadService
{
    /**
     * Download the file from the given URL.
     * 
     * @param string $url
     * @param string $destination
     * @param Command $command
     * @throws Exception
     */
    public function download(string $url, string $destination, Command $command)
    {
        $command->info("Downloading update from: $url");

        $requestTimeout = config('auto-updater.request_timeout', 120);

        $response = Http::timeout($requestTimeout)->get($url);

        if ($response->failed()) {
            throw new Exception("Failed to download update: {$response->status()}");
        }

        File::put($destination, $response->body());
        $command->info("Download completed: $destination");
    }
}