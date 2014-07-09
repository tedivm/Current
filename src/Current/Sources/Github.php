<?php

namespace Current\Sources;

class Github extends Supplied
{
    protected $vendor;
    protected $project;

    protected $releases;

    protected $gitHubUrl = 'https://github.com/';

    public function initialize($url)
    {
        if (substr($url, 0, strlen($this->gitHubUrl)) != $this->gitHubUrl) {
            throw new \RuntimeException('Requires github projects.');
        }

        $url = rtrim($url, '/');
        $project = substr($url, strlen($this->gitHubUrl));
        list($this->vendor, $this->project) = explode('/', $project);

        $apiUrl = $this->getProjectUrl(true);
        $projectUrl = $this->getProjectUrl(false);

        $releasesJson = file_get_contents($apiUrl . 'releases', false, stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'user_agent' => $project . ' updater using tedivm/current library.',
                'timeout' => '2'
            )
        )));

        $releaseList = json_decode($releasesJson, true);
        $processedReleases = array();
        foreach ($releaseList as $release) {

            $updateVersion = array();
            $updateVersion['version'] = $release['tag_name'];
            $updateVersion['stable'] = !$release['draft'] && !$release['prerelease'];

            foreach ($release['assets'] as $asset) {

                $dotPos = strpos($asset['name'], '.');

                if ($dotPos !== false) {
                    $type = substr($asset['name'], $dotPos);
                } else {
                    $type = $asset['name'];
                }

                $updateVersion['assets'][] = array(
                    'name' => $asset['name'],
                    'path' => $asset['browser_download_url'],
                    'type' => $type,
                    'size' => $asset['size'],
                    'source' => false
                );
            }

            if (isset($release['tarball_url'])) {
                $updateVersion['assets'][] = array(
                    'name' => $release['tag_name'] . '.tar.gz',
                    'path' => $projectUrl . 'archive/' . $release['tag_name'] . '.tar.gz',
                    'type' => '.tar.gz',
                    'source' => true
                );
            }

            if (isset($release['zipball_url'])) {
                $updateVersion['assets'][] = array(
                    'name' => $release['tag_name'] . '.zip',
                    'path' => $projectUrl . 'archive/' . $release['tag_name'] . '.zip',
                    'type' => '.zip',
                    'source' => true
                );
            }

            $processedReleases[] = $updateVersion;
        }
        $this->releases = $processedReleases;
    }

    protected function getProjectUrl($api = false)
    {
        if ($api) {
            $url = 'https://api.github.com/repos/';
        } else {
            $url = 'https://github.com/';
        }

        $url .= $this->vendor . '/' . $this->project . '/';

        return $url;
    }

}
