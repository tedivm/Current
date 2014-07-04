<?php

namespace Current\Sources;

use Current\Transports\Http;

class Github
{
    protected $vendor;
    protected $project;

    protected $gitHubUrl = 'https://github.com/';

    public function initialize($url)
    {
        if (substr($url, 0, strlen($this->gitHubUrl)) != $this->gitHubUrl) {
            throw new \RuntimeException('Requires github projects.');
        }

        $project = substr($url, strlen($this->gitHubUrl));
        list($this->vendor, $this->project) = explode('/', $project);
    }

    public function getReleases()
    {
        $apiUrl = $this->getProjectUrl(true);
        $projectUrl = $this->getProjectUrl(false);

        $releasesJson = file_get_contents($apiUrl . '/releases');
        $releaseList = json_decode($releasesJson, true);

        $manifest = array();
        foreach ($releaseList as $release) {

            // No assets, no point in continuing.
            if (!isset($release['assets'])) {
                continue;
            }

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
                    'path' => $projectUrl . 'releases/download/' . $release['tag_name'] . '/' . $asset['name'],
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

            $manifest[] = $updateVersion;
        }

        return $manifest;
    }

    public function getTransport($asset)
    {
        return new Http($asset);
    }

    protected function getProjectUrl($api = false)
    {
        if ($api) {
            $url = 'https://api.github.com/';
        } else {
            $url = 'https://github.com/';
        }

        $url .= $this->vendor . '/' . $this->project . '/';

        return $url;
    }

}
