<?php

namespace Belime\DeepCopyLegacyTest\Filter\Doctrine;

use BadMethodCallException;
use Belime\DeepCopyLegacy\Filter\Doctrine\DoctrineProxyFilter;
use PHPUnit_Framework_TestCase;

/**
 * @covers \DeepCopy\Filter\Doctrine\DoctrineProxyFilter
 */
class DoctrineProxyFilterTest extends PHPUnit_Framework_TestCase
{
    public function test_it_loads_the_doctrine_proxy()
    {
        $foo = new Foo();

        $filter = new DoctrineProxyFilter();

        $filter->apply(
            $foo,
            'unknown',
            function($item) {
                throw new BadMethodCallException('Did not expect to be called.');
            }
        );

        $this->assertTrue($foo->isLoaded);
    }
}

class Foo
{
    public $isLoaded = false;

    public function __load()
    {
        $this->isLoaded = true;
    }
}
