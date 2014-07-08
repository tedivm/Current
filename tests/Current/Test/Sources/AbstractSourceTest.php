<?php

namespace Current\Test\Sources;

abstract class AbstractSourceTest extends \PHPUnit_Framework_TestCase
{
    protected static $url = 'https://github.com/tedivm/Spark/';

    /**
     * @return \Current\Interfaces\Source
     */
    abstract public function getSource();

    public function testInitialize()
    {
        $transport = $this->getSource();
        $releases = $transport->getReleases();

        $this->assertGreaterThan(0, count($releases));

        foreach ($releases as $release) {

            $this->assertArrayHasKey('version', $release);
            $this->assertArrayHasKey('stable', $release);
            $this->assertArrayHasKey('assets', $release);
            $this->assertGreaterThan(0, count($release['assets']));

            foreach ($release['assets'] as $asset) {
                $this->assertArrayHasKey('name', $asset);
                $this->assertArrayHasKey('path', $asset);
                $this->assertArrayHasKey('type', $asset);
                $this->assertArrayHasKey('source', $asset);
            }

        }
    }

}
