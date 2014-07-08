<?php

namespace Current\Test\Sources;

use Current\Sources\Github;

class GithubTest extends AbstractSourceTest
{
    protected static $url = 'https://github.com/tedivm/Spark/';

    public function getSource()
    {
        $transport = new Github();
        $transport->initialize(static::$url);

        return $transport;

    }
}
