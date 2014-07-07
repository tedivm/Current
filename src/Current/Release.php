<?php

namespace Current;

use Current\Interfaces\Progress;
use Current\Interfaces\Source;
use Current\Interfaces\Transport;

class Release
{
    protected $config;
    protected $source;
    protected $assets = array();

    public function __construct(array $config, Source $source)
    {
        $this->config = $config;
        $this->source = $source;

        foreach ($config['assets'] as $asset) {
            $this->assets[$asset['type']] = $asset;
        }

    }

    public function isStable()
    {
        return (bool) $this->config['stable'];
    }

    public function getVersion()
    {
        return new Version($this->config['version']);
    }

    public function hasType($type = 'phar')
    {
        return isset($this->assets[$type]);
    }

    public function saveToTemp($type = 'phar', Progress $progress = null)
    {
        $transport = $this->getTransport($type);
        $tmp = sys_get_temp_dir() . '/current-' . md5(__DIR__) . '/';

        if (!file_exists($tmp)) {
            mkdir($tmp);
        }

        $file = tempnam($tmp, 'current-update');

        if ($transport->saveToFile($file, $progress)) {
            return $file;
        } else {
            return false;
        }
    }

    /**
     * @param  string         $type
     * @return Transport|bool
     */
    protected function getTransport($type = 'phar')
    {
        if (isset($this->assets[$type])) {
            return $this->source->getTransport($this->assets[$type]);
        } else {
            return false;
        }
    }
}
