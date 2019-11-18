<?php

namespace Belime\DeepCopyLegacy\f004;

use BadMethodCallException;

class UnclonableItem
{
    private function __clone()
    {
        throw new BadMethodCallException('Unsupported call.');
    }
}
