<?php

namespace Current\Sources;

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
                $assetArray = array();
                $assetArray['name'] = $asset['name'];
                $assetArray['url'] = $projectUrl . 'releases/download/' . $release['tag_name'] . '/' . $asset['name'];

                $updateVersion['assets'][] = $asset;
            }

            $manifest[] = $updateVersion;
        }

        return $manifest;
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