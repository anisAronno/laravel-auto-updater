<?php 
namespace AnisAronno\LaravelAutoUpdater\Services\VCSProvider;

use AnisAronno\LaravelAutoUpdater\Services\VCSProvider\AbstractVCSProvider;

/**
 * Class GitHubProvider
 * 
 * VCS provider for GitHub.
 */
class GitHubProvider extends AbstractVCSProvider
{
    /**
     * Get the API URL.
     *
     * @return string
     */
    public function getApiUrl(): string
    {
        list($user, $repo) = $this->extractUserAndRepo();
        return sprintf('https://api.github.com/repos/%s/%s/releases', $user, $repo);
    }

    /**
     * Extract the user and repository from the release URL.
     *
     * @param string|null $version
     * @return string
     */
    protected function buildApiUrl(?string $version): string
    {
        $baseUrl = $this->getApiUrl();
        return $version ? "{$baseUrl}/tags/v{$version}" : "{$baseUrl}/latest";
    }

    /**
     * Parse the release data.
     *
     * @param array $data The API response data.
     * @return array The formatted release data.
     */
    protected function parseReleaseData(array $data): array
    {
        return [
            'version' => ltrim($data['tag_name'] ?? '', 'v'),
            'download_url' => $data['zipball_url'] ?? '',
            'changelog' => $data['body'] ?? 'No changelog available',
        ];
    }
}