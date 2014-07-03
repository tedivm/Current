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
                $updateVersion['assets'][] = array(
                    'name' => $asset['name'],
                    'path' => $projectUrl . 'releases/download/' . $release['tag_name'] . '/' . $asset['name'],
                    'source' => false
                );
            }

            if (isset($release['tarball_url'])) {
                $updateVersion['assets'][] = array(
                    'name' => $release['tag_name'] . '.tar.gz',
                    'path' => $projectUrl . 'archive/' . $release['tag_name'] . '.tar.gz',
                    'source' => true
                );
            }

            if (isset($release['zipball_url'])) {
                $updateVersion['assets'][] = array(
                    'name' => $release['tag_name'] . '.zip',
                    'path' => $projectUrl . 'archive/' . $release['tag_name'] . '.zip',
                    'source' => true
                );
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
