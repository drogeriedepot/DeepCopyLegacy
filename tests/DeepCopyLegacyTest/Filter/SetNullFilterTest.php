<?php

namespace Belime\DeepCopyLegacyTest\Filter;

use Belime\DeepCopyLegacy\Filter\SetNullFilter;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @covers \DeepCopy\Filter\SetNullFilter
 */
class SetNullFilterTest extends PHPUnit_Framework_TestCase
{
    public function test_it_sets_the_given_property_to_null()
    {
        $filter = new SetNullFilter();

        $object = new stdClass();
        $object->foo = 'bar';
        $object->bim = 'bam';

        $filter->apply($object, 'foo', null);

        $this->assertNull($object->foo);
        $this->assertEquals('bam', $object->bim);
    }
}
