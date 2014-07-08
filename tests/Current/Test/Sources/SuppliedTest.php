<?php

namespace Current\Test\Sources;

use Current\Sources\Supplied;

class SuppliedTest extends AbstractSourceTest
{
    protected $releases = array(

        array(
            'version' => 'v1.3.4',
            'stable' => true,
            'assets' => array(
                array(
                    'name' => 'FileName.phar',
                    'path' => 'path/to/v1.3.4/FileName.phar',
                    'type' => 'phar',
                    'source' => false
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
                    'type' => 'phar',
                    'source' => false
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
                    'type' => 'phar',
                    'source' => false
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
                    'type' => 'phar',
                    'source' => false
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
                    'type' => 'phar',
                    'source' => false
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
                    'type' => 'phar',
                    'source' => false
                )
            ),
        ),

    );

    public function getSource()
    {
        $source = new Supplied();
        $source->initialize($this->releases);

        return $source;
    }
}
