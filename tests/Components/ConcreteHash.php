<?php
namespace PhoneCom\Mason\Tests\Components;

use PhoneCom\Mason\Builder\Components\Hash;

class ConcreteHash extends Hash
{
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }
}
