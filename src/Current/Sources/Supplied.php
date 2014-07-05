<?php

namespace Current\Sources;

use Current\Interfaces\Source;
use Current\Transports\Http;

class Supplied implements Source
{
    protected $releases;

    public function initialize($releases)
    {
        $this->releases = $releases;
    }

    public function getReleases()
    {
        return $this->releases;
    }

    public function getTransport($asset)
    {
        return new Http($asset);
    }
}
