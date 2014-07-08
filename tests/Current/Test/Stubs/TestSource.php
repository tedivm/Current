<?php

namespace Current\Test\Stubs;

use Current\Interfaces\Source;
use Current\Transports\Http;

class TestSource implements Source
{
    protected $releases = array(

        array(
            'version' => 'v1.3.4',
            'stable' => true,
            'assets' => array(
                array(
                    'name' => 'FileName.phar',
                    'path' => 'path/to/v1.3.4/FileName.phar',
                    'type' => 'phar'
                )
            ),
        ),

        array(
            'version' => 'v1.3.10',
            'stable' => true,
            'assets' => array(
                array(
                    'name' => 'FileName.phar',
                    'path' => 'path/to/v1.3.10/FileName.phar',
                    'type' => 'phar'
                )
            ),
        ),

        array(
            'version' => 'v1.4.10-beta',
            'stable' => true,
            'assets' => array(
                array(
                    'name' => 'FileName.phar',
                    'path' => 'path/to/v1.4.10-beta/FileName.phar',
                    'type' => 'phar'
                )
            ),
        ),

        array(
            'version' => 'v1.4.10',
            'stable' => true,
            'assets' => array(
                array(
                    'name' => 'FileName.phar',
                    'path' => 'path/to/v1.4.10/FileName.phar',
                    'type' => 'phar'
                )
            ),
        ),

        array(
            'version' => 'v1.4.11-beta',
            'stable' => true,
            'assets' => array(
                array(
                    'name' => 'FileName.phar',
                    'path' => 'path/to/v1.4.10/FileName.phar',
                    'type' => 'phar'
                )
            ),
        ),

        array(
            'version' => 'v2.2.12',
            'stable' => true,
            'assets' => array(
                array(
                    'name' => 'FileName.phar',
                    'path' => 'path/to/v2.2.12/FileName.phar',
                    'type' => 'phar'
                )
            ),
        ),

    );

    public function initialize($releases)
    {
        //$this->releases = $releases;
    }

    public function getReleases()
    {
        return $this->releases;
    }

    public function getTransport($asset)
    {
        return new Http($asset);
    }
}
