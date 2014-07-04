<?php

namespace Current;

use Current\Interfaces\Progress;
use Current\Interfaces\Source;

class Release
{
    protected $config;
    protected $source;

    public function __construct(array $config, Source $source)
    {
        $this->config = $config;
        $this->source = $source;
    }

    public function isStable()
    {
        return (bool) $this->config['stable'];
    }

    public function hasType($type = 'phar', $source = false)
    {

    }

    public function saveToTemp($type = 'phar', Progress $progress = null)
    {
        $tmp = sys_get_temp_dir() . '/current-' . md5(__DIR__) . '/';

        if (!file_exists($tmp)) {
            mkdir($tmp);
        }

        $file = tempnam(sys_get_temp_dir(), 'current-update');
        $transport = $this->getTransport($type, false);

        if ($transport->saveToFile($file, $progress)) {
            return $file;
        } else {
            return false;
        }

    }

    public function saveSourceToTemp($type = 'tar.gz')
    {

    }

    protected function getTransport($type = 'phar', $source = false)
    {
        foreach ($this->config['assets'] as $asset) {
            if ($asset['type'] == $type || $asset['type'] == '.' . $type) {
                if ($asset['source'] == $source) {
                    // Is the right type, and

                    // Yup, we return the first one that matches.
                    return $this->source->getTransport($asset);
                }
            }
        }

        return false;
    }
}
