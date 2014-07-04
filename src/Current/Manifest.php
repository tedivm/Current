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

    public function getLatestVersion($stable = true, $major = null, $minor = null)
    {
        $stableVersion = $this->getLatestVersionByType(true, $major, $minor);

        if ($stable === true) {
            return $stableVersion;
        }

        $developmentVersion = $this->getLatestVersionByType(false, $major, $minor);

        if ($stableVersion === false) {
            return $developmentVersion;
        }

        if ($developmentVersion === false) {
            return $stableVersion;
        }

        if (Version::compare($stableVersion, $developmentVersion)) {
            return $stableVersion;
        } else {
            return $developmentVersion;
        }
    }

    public function getReleaseFromVersion(Version $version)
    {
        if (!isset($this->releases[$version->getLongString()])) {
            return false;
        }

        return new Release($this->releases[$version->getLongString()], $this->source);
    }

    public function getAvailableUpdates($version = null)
    {
        $availableUpdates = 0;

        if (!($version instanceof Version)) {
            $version = new Version($version);
        }

        if (!isset($version)) {
            return Update::MAJOR;
        }

        $latest = $this->getLatestVersion(true)->getMajor();
        if (Version::compare($latest, $version)) {
            $availableUpdates = $availableUpdates & Update::MAJOR;
        }

        $macro = $this->getReleaseFromVersion(true, $version->getMajor());
        if (Version::compare($macro, $version)) {
            $availableUpdates = $availableUpdates & Update::MINOR;
        }

        $micro = $this->getReleaseFromVersion(true, $version->getMajor(), $version->getMinor());
        if (Version::compare($micro, $version)) {
            $availableUpdates = $availableUpdates & Update::PATCH;
        }

        return $availableUpdates;
    }

    protected function getLatestVersionByType($stable = true, $major = null, $minor = null)
    {
        $versionProperty = $stable === true ? 'latestStable' : 'latestDevelopment';

        if (!isset($major)) {
            $latestMajor = end($this->$versionProperty);
        } elseif (isset($this->{$versionProperty}[$major])) {
            $latestMajor = $this->{$versionProperty}[$major];
        } else {
            return false;
        }

        if (!isset($minor)) {
            return end($latestMajor);
        } elseif (isset($latestMajor[$minor])) {
            return $latestMajor[$minor];
        } else {
            return false;
        }
    }
}
