<?php

namespace Current;

use Current\Interfaces\Source;

class Update
{
    protected $currentVersion;
    protected $manifest;

    protected static $sources = array('https://github.com/' => 'Github');
    protected static $defaultSource = '';

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

        $manifest = new Manifest($source);

        return new self($manifest);
    }

    public function __construct(Manifest $manifest)
    {
        $this->manifest = $manifest;
    }

    public function setCurrentVersion($version)
    {
        if ($version instanceof Version) {
            $this->currentVersion = $version;
        } elseif (is_string($version)) {
            $version = new Version($version);
            $this->currentVersion = $version;
        } else {
            throw new \RuntimeException('Invalid version.');
        }
    }
}
