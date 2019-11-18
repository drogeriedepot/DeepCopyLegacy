<?php

namespace Belime\DeepCopyLegacy\f001;

class B extends A
{
    private $bProp;

    public function getBProp()
    {
        return $this->bProp;
    }

    public function setBProp($prop)
    {
        $this->bProp = $prop;

        return $this;
    }
}
