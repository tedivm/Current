<?php

namespace Current\Interfaces;

interface Source
{
    public function initialize($url);
    public function getReleases();
    public function getTransport($asset);

}
