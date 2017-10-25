<?php

namespace Ephrin\Immutable\Tests;

use Ephrin\Immutable\DocProperty;
use Ephrin\Immutable\DocStructure;

class DocStructureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DocStructure
     */
    private $structure;

    protected function setUp()
    {
        $this->structure = new DocStructure(
            \stdClass::class,
            [
                'some' => new DocProperty(
                    'string',
                    true
                ),
                'immutable' => new DocProperty(
                    'integer',
                    false
                )
            ]
        );
    }

    public function testHasProperty()
    {
        $this->assertFalse($this->structure->hasProperty('none'));
        $this->assertTrue($this->structure->hasProperty('some'));
    }
}
