<?php

namespace Current\Test;

use Current\Manifest;
use Current\Test\Sources\TestSource;
use Current\Update;
use Current\Version;

class ManifestTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $manifest = new Manifest(new TestSource());
        $this->assertInstanceOf('Current\Manifest', $manifest);

        return $manifest;
    }

    public function testGetLatestRelease()
    {
        $manifest = $this->testConstruct();

        $lastVersion = $manifest->getLatestVersion();

        $this->assertInstanceOf('Current\Version', $lastVersion);
        $lastVersionString = $lastVersion->getLongString();
        $this->assertEquals('v2.2.12', $lastVersionString);

        $lastVersionOne = $manifest->getLatestVersion(true, 1);
        $this->assertInstanceOf('Current\Version', $lastVersionOne);
        $lastVersionOneString = $lastVersionOne->getLongString();
        $this->assertEquals('v1.4.10', $lastVersionOneString);

        $lastVersionOnePrerelease = $manifest->getLatestVersion(false, 1);
        $this->assertInstanceOf('Current\Version', $lastVersionOnePrerelease);
        $lastVersionOnePrereleaseString = $lastVersionOnePrerelease->getLongString();
        $this->assertEquals('v1.4.11-beta', $lastVersionOnePrereleaseString);

        $lastVersionOne = $manifest->getLatestVersion(true, 1, 3);
        $this->assertInstanceOf('Current\Version', $lastVersionOne);
        $lastVersionOneString = $lastVersionOne->getLongString();
        $this->assertEquals('v1.3.10', $lastVersionOneString);

        $this->assertFalse($manifest->getLatestVersion(true, 3));
    }

    public function testGetReleaseFromVersion()
    {
        $manifest = $this->testConstruct();
        $lastVersion = $manifest->getLatestVersion();
        $release = $manifest->getReleaseFromVersion($lastVersion);
        $this->assertInstanceOf('Current\\Release', $release);
    }

    public function testGetAvailableUpdates()
    {
        $manifest = $this->testConstruct();

        $availableUpdates = $manifest->getAvailableUpdates();
        $this->assertTrue((bool) (Update::MAJOR & $availableUpdates));
        $this->assertFalse((bool) (Update::MINOR & $availableUpdates));
        $this->assertFalse((bool) (Update::PATCH & $availableUpdates));

        $currentVersion = new Version('1.0.0');
        $availableUpdates = $manifest->getAvailableUpdates($currentVersion);
        $this->assertTrue((bool) (Update::MAJOR & $availableUpdates));
        $this->assertTrue((bool) (Update::MINOR & $availableUpdates));
        $this->assertFalse((bool) (Update::PATCH & $availableUpdates));

        $currentVersion = new Version('1.3.0');
        $availableUpdates = $manifest->getAvailableUpdates($currentVersion);
        $this->assertTrue((bool) (Update::MAJOR & $availableUpdates));
        $this->assertTrue((bool) (Update::MINOR & $availableUpdates));
        $this->assertTrue((bool) (Update::PATCH & $availableUpdates));

        $currentVersion = new Version('1.4.10');
        $availableUpdates = $manifest->getAvailableUpdates($currentVersion, true);
        $this->assertTrue((bool) (Update::MINOR & $availableUpdates));

        $currentVersion = new Version('2.2.10');
        $availableUpdates = $manifest->getAvailableUpdates($currentVersion, true);
        $this->assertTrue((bool) (Update::MINOR & $availableUpdates));

        $newVersion = new Version('3.0.0');
        $availableUpdates = $manifest->getAvailableUpdates($newVersion);
        $this->assertFalse((bool) (Update::MAJOR & $availableUpdates));
        $this->assertFalse((bool) (Update::MINOR & $availableUpdates));
        $this->assertFalse((bool) (Update::PATCH & $availableUpdates));
    }
}
