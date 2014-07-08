<?php

namespace Current\Test\Sources;

abstract class AbstractSourceTest extends \PHPUnit_Framework_TestCase
{

    protected $expectedTransport = '\\Current\\Transports\\Http';

    /**
     * @return \Current\Interfaces\Source
     */
    abstract public function getSource();

    public function testInitialize()
    {
        $source = $this->getSource();
        $releases = $source->getReleases();

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

    public function testGetTransport()
    {
        $source = $this->getSource();
        $releases = $source->getReleases();
        $transport = $source->getTransport($releases[0]['assets'][0]);
        $this->assertInstanceOf($this->expectedTransport, $transport);
    }

}
