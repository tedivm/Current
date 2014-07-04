<?php

namespace Current\Transports;

use Current\Interfaces\Progress;
use Current\Interfaces\Transport;
use SplFileObject;

class Http implements Transport
{
    protected $name;
    protected $path;
    protected $type;
    protected $source;
    protected $size;
    protected $md5;
    protected $sha;

    public function __construct($config)
    {
        $this->name = $config['name'];
        $this->path = $config['path'];
        $this->type = $config['type'];

        $options = array(
            'source', 'size', 'md5', 'sha'
        );

        foreach ($options as $option) {
            $this->$option = isset($config[$option]) ? $config[$option] : false;
        }
    }

    public function saveToFile($file, Progress $progress = null)
    {
        $filename = $file;

        $dest = new SplFileObject($filename, 'wb', false);
        $sourceHandle = fopen($this->path, "rb");

        while (!feof($sourceHandle)) {
            $dest->fwrite(fread($sourceHandle, 8192));
            if (isset($progressCallback)) {
                $progress->setFileProgress($dest->ftell(), $this->size);
            }
        }
        $progress->setFileComplete();
        fclose($sourceHandle);
    }

}
