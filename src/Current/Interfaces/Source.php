<?php

namespace Current\Interfaces;

interface Source
{
    public function initialize($resource);
    public function getReleases();
    public function getTransport($asset);

}
