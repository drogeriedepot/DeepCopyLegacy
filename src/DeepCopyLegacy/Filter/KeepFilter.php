<?php

namespace Belime\DeepCopyLegacy\Filter;

class KeepFilter implements Filter
{
    /**
     * Keeps the value of the object property.
     *
     * {@inheritdoc}
     */
    public function apply($object, $property, $objectCopier)
    {
        // Nothing to do
    }
}
