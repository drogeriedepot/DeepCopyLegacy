<?php

namespace Belime\DeepCopyLegacyTest\Filter\Doctrine;

use Belime\DeepCopyLegacy\Filter\Doctrine\DoctrineCollectionFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @covers \DeepCopy\Filter\Doctrine\DoctrineCollectionFilter
 */
class DoctrineCollectionFilterTest extends PHPUnit_Framework_TestCase
{
    public function test_it_copies_the_object_property_array_collection()
    {
        $object = new stdClass();
        $oldCollection = new ArrayCollection();
        $oldCollection->add($stdClass = new stdClass());
        $object->foo = $oldCollection;

        $filter = new DoctrineCollectionFilter();

        $filter->apply(
            $object,
            'foo',
            function($item) {
                return null;
            }
        );

        $this->assertTrue($object->foo instanceof Collection);
        $this->assertNotSame($oldCollection, $object->foo);
        $this->assertCount(1, $object->foo);

        $objectOfNewCollection = $object->foo->get(0);

        $this->assertNotSame($stdClass, $objectOfNewCollection);
    }
}
