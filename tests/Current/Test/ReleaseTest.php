<?php

namespace Current\Test;

use Current\Release;
use Current\Test\Sources\TestSource;

class ReleaseTest extends \PHPUnit_Framework_TestCase
{

    protected $stableRelease = array(
        'version' => 'v1.3.4',
        'stable' => true,
        'assets' => array(
            array(
                'name' => 'FileName.phar',
                'type' => 'phar'
            )
        ),
    );

    protected $developmentRelease = array(
        'version' => 'v1.3.4-beta',
        'stable' => true,
        'assets' => array(
            array(
                'name' => 'FileName.phar',
                'type' => 'phar'
            )
        ),
    );

    public function testConstruct($stable = true)
    {
        $testSource = new TestSource();
        $config = $stable ? $this->stableRelease : $this->developmentRelease;

        $path = __DIR__ . '/../../packages/';
        $path .= '1.3.4';

        if (!$stable) {
            $path .= '-beta';
        }

        $path .= '/TestPackage.phar';

        $config['assets'][0]['path'] = realpath($path);

        $config['assets'][0]['sha'] = $stable
            ? 'fa3841ac76b7356c01d50825ce9b1b7885b0adcb'
            : '9af77837e7632e18fc4431ab4b0a71de5baf4165';

        $release = new Release($config, $testSource);
        $this->assertInstanceOf('Current\\Release', $release);

        return $release;
    }

    public function testIsStable()
    {
        $stableRelease = $this->testConstruct(true);
        $this->assertTrue($stableRelease->isStable());
        $developmentRelease = $this->testConstruct(false);
        $this->assertTrue($developmentRelease->isStable());
    }

    public function testGetVersion()
    {
        $stableRelease = $this->testConstruct(true);
        $version = $stableRelease->getVersion();
        $this->assertEquals('v1.3.4', $version->getLongString());

        $developmentRelease = $this->testConstruct(false);
        $version = $developmentRelease->getVersion();
        $this->assertEquals('v1.3.4-beta', $version->getLongString());
    }

    public function testHasType()
    {
        $stableRelease = $this->testConstruct(true);
        $this->assertTrue($stableRelease->hasType('phar'));
        $this->assertFalse($stableRelease->hasType('apples'));
    }

    public function testSaveToTemp()
    {

    }
}
