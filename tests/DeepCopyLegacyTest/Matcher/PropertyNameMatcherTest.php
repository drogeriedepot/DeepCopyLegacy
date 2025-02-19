<?php

namespace Belime\DeepCopyLegacyTest\Matcher;

use Belime\DeepCopyLegacy\Matcher\PropertyNameMatcher;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @covers \DeepCopy\Matcher\PropertyNameMatcher
 */
class PropertyNameMatcherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providePairs
     */
    public function test_it_matches_the_given_property($object, $prop, $expected)
    {
        $matcher = new PropertyNameMatcher('foo');

        $actual = $matcher->matches($object, $prop);

        $this->assertEquals($expected, $actual);
    }

    public function providePairs()
    {
        return [
            [new stdClass(), 'foo', true],
            [new stdClass(), 'unknown', false],
        ];
    }
}
