<?php

namespace Current\Test;

use Current\Version;

class VersionTest extends \PHPUnit_Framework_TestCase
{
    protected $sortedVersionList = array(

        '1.0.0-alpha',
        '1.0.0-alpha.1',
        '1.0.0-alpha.2',
        '1.0.0-beta',
        '1.0.0-beta.2.3',
        '1.0.0-beta.2.5',
        '1.0.0-beta.10',
        '1.0.0-rc',
        '1.0.0-rc.1',
        '1.0.0-rc.2',
        '1.0.0',
        '2.0.0',
        '2.3.0',
        '2.3.1',
        '2.3.2',
        '2.3.10',
    );

    public function testCompare()
    {
        $testVersions = $this->sortedVersionList;
        $num = count($this->sortedVersionList) - 1;
        for($i = 0; $i < $num; $i++) {

            $a = $testVersions[$i];
            $b = $testVersions[$i+1];

            $this->assertEquals(-1, Version::compare($a, $b), $a . ' < ' . $b);
            $this->assertEquals(1, Version::compare($b, $a), $b . ' < ' . $a);
            $this->assertEquals(0, Version::compare($a, $a), $a . ' == ' . $a);
        }
    }
}