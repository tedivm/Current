<?php

namespace Current\Test;

use Current\Version;

class VersionTest extends \PHPUnit_Framework_TestCase
{
    protected $sortedVersionList = array(

        '1.0.0-alpha',
        '1.0.0-alpha.1',
        '1.0.0-alpha.2',
        '1.0.0-alpha.beta',
        'v1.0.0-beta',
        '1.0.0-beta.2.3',
        'v1.0.0-beta.2.5',
        '1.0.0-beta.10',
        '1.0.0-rc',
        'v1.0.0-rc.1',
        '1.0.0-rc.2',
        '1.0.0',
        'v2.0.0-beta',
        'v2.0.0',
        '2.3.0',
        '2.3.1',
        'v2.3.2',
        '2.3.10',
    );

    public function testCompare()
    {
        $testVersions = $this->sortedVersionList;
        $num = count($this->sortedVersionList) - 1;
        for ($i = 0; $i < $num; $i++) {

            $a = $testVersions[$i];
            $b = $testVersions[$i+1];
            $this->assertEquals(-1, Version::compare($a, $b), $a . ' < ' . $b);
            $this->assertEquals(1, Version::compare($b, $a), $b . ' < ' . $a);
            $this->assertEquals(0, Version::compare($a, $a), $a . ' == ' . $a);

            $a = new Version($a);
            $b = new Version($b);
            $this->assertEquals(-1, Version::compare($a, $b), $a . ' < ' . $b);
            $this->assertEquals(1, Version::compare($b, $a), $b . ' < ' . $a);
            $this->assertEquals(0, Version::compare($a, $a), $a . ' == ' . $a);

        }

        $a = '1.0.0+123';
        $b = '1.0.0+456';
        $this->assertEquals(0, Version::compare($a, $b), $a . ' == ' . $b);
    }

    public function testGetLongString()
    {
        $versionString = 'v1.0.0-alpha.4+5678';

        $a = new Version($versionString);

        $this->assertEquals($versionString, $a->getLongString());

        $this->assertEquals('v1-alpha.4', $a->getShortString());

        $a = new Version('v1.1.0-alpha+cheeeesey');
        $this->assertEquals('v1.1-alpha', $a->getShortString());

        $a = new Version('v1.1.1-alpha+pooooooofs');
        $this->assertEquals('v1.1.1-alpha', $a->getShortString());

    }
}
