<?php
namespace Phonedotcom\Mason\Tests\Components;

use Phonedotcom\Mason\Builder\Components\Hash;

class ConcreteHash extends Hash
{
    public function setTailgate($tailgate)
    {
        $this->tailgate = $tailgate;
        return $this;
    }
}
