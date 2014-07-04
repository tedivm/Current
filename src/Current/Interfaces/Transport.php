<?php

namespace Current\Interfaces;

interface Transport
{
    public function saveToFile($file, Progress $progress = null);

}
