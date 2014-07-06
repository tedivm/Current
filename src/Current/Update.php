<?php

namespace Current;

use Current\Interfaces\Source;

class Update
{
    /**
     * @var Version
     */
    protected $currentVersion;

    /**
     * @var Manifest
     */
    protected $manifest;

    protected $applicationPath;

    protected static $sources = array('https://github.com/' => 'Github');
    protected static $defaultSource = '';

    const MAJOR = 1;
    const MINOR = 2;
    const PATCH = 4;

    public static function buildFromResource($resource)
    {

        $className = static::$defaultSource;

        if (is_string($resource)) {

            foreach (self::$sources as $sourceUrl => $sourceClass) {
                if (substr($resource, 0, strlen($sourceUrl)) == $sourceUrl) {
                    $className = 'Current\\Sources\\' . $sourceClass;
                    break;
                }
            }

        } elseif (is_array($resource)) {
            $className = 'Current\\Sources\\Supplied';
        }

        /** @var Source $source */
        $source = new $className();
        $source->initialize($resource);

        $manifest = new Manifest($source);

        return new self($manifest);
    }

    public function __construct(Manifest $manifest, $applicationPath = null)
    {
        $this->manifest = $manifest;
        $this->applicationPath = !is_null($applicationPath) ? $applicationPath : realpath($_SERVER['argv'][0]);

    }

    public function setCurrentVersion($version)
    {
        if ($version instanceof Version) {
            $this->currentVersion = $version;
        } elseif (is_string($version)) {
            $this->currentVersion = new Version($version);;
        } else {
            throw new \RuntimeException('Invalid version.');
        }
    }

    public function update($level = self::MAJOR)
    {
        if ($level | self::MAJOR) {
            $updateVersion = $this->manifest->getLatestVersion(true);
        } elseif ($level | self::MINOR) {
            $updateVersion = $this->manifest->getLatestVersion(true, $this->currentVersion->getMajor());
        } else {
            $updateVersion = $this->manifest->getLatestVersion(true, $this->currentVersion->getMajor(), $this->currentVersion->getMinor());
        }

        if ($updateVersion === false) {
            // There are no versions available with the requested limits.
            return true;
        }

        if (isset($this->currentVersion) && Version::compare($this->currentVersion, $updateVersion)) {
            // nothing to do, this is the current version
            return true;
        }

        $release = $this->manifest->getReleaseFromVersion($updateVersion);
        $tmp = $release->saveToTemp();

        return rename($tmp, $this->applicationPath);
    }

}
