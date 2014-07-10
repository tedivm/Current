<?php

namespace Current\Sources;

class Web extends Supplied
{
    protected $releases;

    public function initialize($url)
    {
        $releasesJson = file_get_contents($url);
        $releaseList = json_decode($releasesJson, true);
        $this->releases = $releaseList;
    }
}
