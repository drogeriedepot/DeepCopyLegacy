<?php

namespace Belime\DeepCopyLegacy\TypeFilter;

/**
 * @final
 */
class ShallowCopyFilter implements TypeFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply($element)
    {
        return clone $element;
    }
}
