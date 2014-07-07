<?php

namespace Current\Test\Sources;

use Current\Sources\Github;

class GithubTest extends \PHPUnit_Framework_TestCase
{
    protected static $url = 'https://github.com/tedivm/Spark/';

    public function testInitialize()
    {
        $transport = new Github();
        $transport->initialize(static::$url);
        $releases = $transport->getReleases();

        $this->assertGreaterThan(0, count($releases));

        foreach ($releases as $release) {

            $this->assertArrayHasKey('version', $release);
            $this->assertArrayHasKey('stable', $release);
            $this->assertArrayHasKey('assets', $release);

            foreach ($release['assets'] as $asset) {
                $this->assertArrayHasKey('name', $asset);
                $this->assertArrayHasKey('path', $asset);
                $this->assertArrayHasKey('type', $asset);
                $this->assertArrayHasKey('source', $asset);
            }

        }
    }

}
