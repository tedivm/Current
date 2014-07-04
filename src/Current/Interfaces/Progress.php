<?php

namespace Current\Interfaces;

interface Progress
{
    public function setFileProgress($currentSize, $finalSize = null);

    public function setFileComplete();

}
