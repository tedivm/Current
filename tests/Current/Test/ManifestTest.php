<?php

namespace Current\Test;

use Current\Manifest;
use Current\Version;

class ManifestTest extends \PHPUnit_Framework_TestCase
{

    protected $testManifest = array(

        array(
            'version' => 'v1.3.4',
            'stable' => true,
            'assets' => array(
                array(
                    'name' => 'FileName.phar',
                    'path' => 'path/to/v1.3.4/FileName.phar'
                )
            ),
        ),

        array(
            'version' => 'v1.3.10',
            'stable' => true,
            'assets' => array(
                array(
                    'name' => 'FileName.phar',
                    'path' => 'path/to/v1.3.10/FileName.phar'
                )
            ),
        ),

        array(
            'version' => 'v1.4.10-beta',
            'stable' => true,
            'assets' => array(
                array(
                    'name' => 'FileName.phar',
                    'path' => 'path/to/v1.4.10-beta/FileName.phar'
                )
            ),
        ),

        array(
            'version' => 'v1.4.10',
            'stable' => true,
            'assets' => array(
                array(
                    'name' => 'FileName.phar',
                    'path' => 'path/to/v1.4.10/FileName.phar'
                )
            ),
        ),

        array(
            'version' => 'v2.2.12',
            'stable' => true,
            'assets' => array(
                array(
                    'name' => 'FileName.phar',
                    'path' => 'path/to/v2.2.12/FileName.phar'
                )
            ),
        ),

    );

    public function testConstruct()
    {
        $stub = $this->getMock('Current\Interfaces\Source');
        $stub->expects($this->any())
            ->method('getReleases')
            ->will($this->returnValue($this->testManifest));

        $manfest = new Manifest($stub);
        $this->assertInstanceOf('Current\Manifest', $manfest);

        return $manfest;
    }

    public function testGetLatestRelease()
    {
        $manifest = $this->testConstruct();

        $lastVersion = $manifest->getLatestVersion();

        $this->assertInstanceOf('Current\\Version', $lastVersion);
        $lastVersionString = $lastVersion->getLongString();
        $this->assertEquals('v2.2.12', $lastVersionString);


        $lastVersionOne = $manifest->getLatestVersion(1);

        $this->assertInstanceOf('Current\\Version', $lastVersionOne);
        $lastVersionOneString = $lastVersionOne->getLongString();
        $this->assertEquals('v1.4.10', $lastVersionOneString);

        $this->assertFalse($manifest->getLatestVersion(3));

    }


    public function testGetReleaseFromVersion()
    {
        $manifest = $this->testConstruct();

        $lastVersion = $manifest->getLatestVersion();

        $release = $manifest->getReleaseFromVersion($lastVersion);

        $this->assertArrayHasKey('version', $release);
        $this->assertEquals('v2.2.12', $release['version']);

        $this->assertArrayHasKey('stable', $release);
        $this->assertEquals(true, $release['stable']);

        $this->assertArrayHasKey('assets', $release);

        $asset = $release['assets'][0];

        $this->assertArrayHasKey('name', $asset);
        $this->assertEquals('FileName.phar', $asset['name']);

        $this->assertArrayHasKey('path', $asset);
        $this->assertEquals('path/to/v2.2.12/FileName.phar', $asset['path']);
    }
}
