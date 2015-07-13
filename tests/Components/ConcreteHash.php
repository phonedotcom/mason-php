<?php
namespace PhoneCom\Mason\Tests\Components;

use PhoneCom\Mason\Builder\Components\Hash;

class ConcreteHash extends Hash
{
    public function setTailgate($tailgate)
    {
        $this->tailgate = $tailgate;
        return $this;
    }
}
