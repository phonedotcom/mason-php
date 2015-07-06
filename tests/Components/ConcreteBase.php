<?php
namespace PhoneCom\Mason\Tests\Components;

use PhoneCom\Mason\Builder\Components\Base;

class ConcreteBase extends Base
{
    public function setBananas(array $bananas)
    {
        $this->bananas = $bananas;
        return $this;
    }

    public function setThumb($thumb)
    {
        $this->thumb = $thumb;
        return $this;
    }

    public function setMonkey($monkey)
    {
        $this->monkey = $monkey;
        return $this;
    }

    public function minimize()
    {
        unset($this->thumb);
        parent::minimize();

        return $this;
    }
}
