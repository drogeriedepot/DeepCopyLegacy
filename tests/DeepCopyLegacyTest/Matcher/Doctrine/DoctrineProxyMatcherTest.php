<?php

namespace Belime\DeepCopyLegacyTest\Matcher;

use BadMethodCallException;
use Belime\DeepCopyLegacy\Matcher\Doctrine\DoctrineProxyMatcher;
use Doctrine\Common\Persistence\Proxy;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @covers \DeepCopy\Matcher\Doctrine\DoctrineProxyMatcher
 */
class DoctrineProxyMatcherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providePairs
     */
    public function test_it_matches_the_given_objects($object, $expected)
    {
        $matcher = new DoctrineProxyMatcher();

        $actual = $matcher->matches($object, 'unknown');

        $this->assertEquals($expected, $actual);
    }

    public function providePairs()
    {
        return [
            [new FooProxy(), true],
            [new stdClass(), false],
        ];
    }
}

class FooProxy implements Proxy
{
    /**
     * @inheritdoc
     */
    public function __load()
    {
        throw new BadMethodCallException();
    }

    /**
     * @inheritdoc
     */
    public function __isInitialized()
    {
        throw new BadMethodCallException();
    }
}
