<?php

namespace Current;

use Current\Interfaces\Progress;
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

    protected $type = 'phar';

    protected $level;

    protected $updateVersion;

    /**
     * @var Interfaces\Progress
     */
    protected $progress = null;

    protected $sources = array('https://github.com/' => 'Github');
    protected $defaultSource = 'Current\\Sources\\Web';

    const MAJOR = 1;
    const MINOR = 2;
    const PATCH = 4;

    public function __construct(Source $source, $applicationPath = null, $level = Update::MAJOR)
    {
        if (!is_object($source) || !($source instanceof Source)) {
            $source = $this->makeSourceFromResource($source);
        }

        $this->source = $source;
        $this->manifest = new Manifest($source);
        $this->applicationPath = !is_null($applicationPath) ? $applicationPath : realpath($_SERVER['argv'][0]);
        $this->level = $level;

    }

    protected function makeSourceFromResource($resource)
    {
        $className = $this->defaultSource;

        if (is_string($resource)) {

            foreach ($this->sources as $sourceUrl => $sourceClass) {
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

        return new self($source);
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

    public function setProgressMonitor(Progress $progress)
    {
        $this->progress = $progress;
    }

    public function getProgressMonitor()
    {
        if (isset($this->progress)) {
            return $this->progress;
        } else {
            return false;
        }
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getUpdateVersion()
    {
        if (isset($this->updateVersion)) {
            return $this->updateVersion;
        }

        $level = $this->level;
        if ((bool) ($level & self::MAJOR)) {
            $updateVersion = $this->manifest->getLatestVersion(true);
        } elseif ((bool) ($level & self::MINOR)) {
            $updateVersion = $this->manifest->getLatestVersion(true, $this->currentVersion->getMajor());
        } else {
            $updateVersion = $this->manifest->getLatestVersion(true, $this->currentVersion->getMajor(), $this->currentVersion->getMinor());
        }

        if ($updateVersion === false) {
            $this->updateVersion = false;

            return false;
        }

        if (!isset($this->currentVersion) && (bool) Version::compare($this->currentVersion, $updateVersion)) {
            $this->updateVersion = false;

            return false;
        }

        $this->updateVersion = $updateVersion;

        return $updateVersion;
    }

    public function isUpdateAvailable()
    {
        return $this->getUpdateVersion() !== false;
    }

    public function saveToTemp()
    {
        $updateVersion = $this->getUpdateVersion();
        $release = $this->manifest->getReleaseFromVersion($updateVersion);

        return $release->saveToTemp($this->type, $this->progress);
    }

    public function update()
    {
        if (!$this->isUpdateAvailable()) {
            return true;
        }

        $tmp = $this->saveToTemp();

        return rename($tmp, $this->applicationPath);
    }

}
