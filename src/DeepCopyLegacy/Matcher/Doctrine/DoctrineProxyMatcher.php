<?php

namespace Belime\DeepCopyLegacy\Matcher\Doctrine;

use Belime\DeepCopyLegacy\Matcher\Matcher;
use Doctrine\Common\Persistence\Proxy;

/**
 * @final
 */
class DoctrineProxyMatcher implements Matcher
{
    /**
     * Matches a Doctrine Proxy class.
     *
     * {@inheritdoc}
     */
    public function matches($object, $property)
    {
        return $object instanceof Proxy;
    }
}
