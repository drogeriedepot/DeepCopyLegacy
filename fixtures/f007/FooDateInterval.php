<?php

namespace Belime\DeepCopyLegacy\f007;

use DateInterval;

class FooDateInterval extends DateInterval
{
    public $cloned = false;

    public function __clone()
    {
        $this->cloned = true;
    }
}
