<?php

namespace Current;

use Current\Interfaces\Source;

class Manifest
{
    protected $source;

    protected $releases = array();
    protected $versions = array();

    protected $latestStable = array();
    protected $latestDevelopment = array();

    public function __construct(Source $source)
    {
        $this->source = $source;

        foreach ($source->getReleases() as $releaseConfig) {

            $version = new Version($releaseConfig['version'], !$releaseConfig['stable'] ? true : null);
            $this->releases[$version->getLongString()] = $releaseConfig;
            $this->versions[] = $version;
        }

        usort($this->versions, 'Current\\Version::compare');

        /** @var Version $versionObject */
        foreach ($this->versions as $versionObject) {

            $major = $versionObject->getMajor();
            $minor = $versionObject->getMinor();
            if ($versionObject->isStable()) {
                $this->latestStable[$major][$minor] = $versionObject;
            } else {
                $this->latestDevelopment[$major][$minor] = $versionObject;
            }
        }
    }

    public function getLatestVersion($macro = null, $minor = null)
    {
        if (!isset($macro)) {
            $latestMacro = end($this->latestStable);
        } elseif (isset($this->latestStable[$macro])) {
            $latestMacro = $this->latestStable[$macro];
        } else {
            return false;
        }

        if (!isset($minor)) {
            return end($latestMacro);
        } elseif (isset($latestMacro[$minor])) {
            return $latestMacro[$minor];
        } else {
            return false;
        }
    }

    public function getReleaseFromVersion(Version $version)
    {
        return $this->releases[$version->getLongString()];
    }
}
