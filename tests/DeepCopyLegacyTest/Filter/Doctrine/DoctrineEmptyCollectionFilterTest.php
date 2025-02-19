<?php

namespace Belime\DeepCopyLegacyTest\Filter\Doctrine;

use Belime\DeepCopyLegacy\Filter\Doctrine\DoctrineEmptyCollectionFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @covers \DeepCopy\Filter\Doctrine\DoctrineEmptyCollectionFilter
 */
class DoctrineEmptyCollectionFilterTest extends PHPUnit_Framework_TestCase
{
    public function test_it_sets_the_object_property_to_an_empty_doctrine_collection()
    {
        $object = new stdClass();

        $collection = new ArrayCollection();
        $collection->add(new stdClass());

        $object->foo = $collection;

        $filter = new DoctrineEmptyCollectionFilter();

        $filter->apply(
            $object,
            'foo',
            function($item) {
                return null;
            }
        );

        $this->assertTrue($object->foo instanceof Collection);
        $this->assertNotSame($collection, $object->foo);
        $this->assertTrue($object->foo->isEmpty());
    }
}
