<?php

namespace Belime\DeepCopyLegacy\f008;

class A
{
    private $foo;

    public function __construct($foo)
    {
        $this->foo = $foo;
    }

    public function getFoo()
    {
        return $this->foo;
    }
}
