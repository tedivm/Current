<?php

namespace Current;

use Current\Interfaces\Source;

class Manifest
{
    protected static $sources = array('https://github.com/' => 'Github');
    protected static $defaultSource = '';

    protected $source;

    protected $releases = array();
    protected $versions = array();

    protected $latests = array();

    public static function buildFromUrl($url)
    {
        $className = static::$defaultSource;
        foreach (self::$sources as $sourceUrl => $sourceClass) {
            if (substr($url, 0, strlen($sourceUrl)) == $sourceUrl) {
                $className = 'Current\\Sources\\' . $sourceClass;
                break;
            }
        }

        /** @var Source $source */
        $source = new $className();
        $source->initialize($url);

        return new self($source);
    }

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
            $versionObject->getMajor();
            $this->latests[$versionObject->getMajor()] = $versionObject;
        }

    }

    public function getLatestVersion($macro = null)
    {
        if (!isset($macro)) {
            return end($this->latests);
        }

        if (isset($this->latests[$macro])) {
            return $this->latests[$macro];
        } else {
            return false;
        }
    }

    public function getReleaseFromVersion(Version $version)
    {
        return $this->releases[$version->getLongString()];
    }

}
