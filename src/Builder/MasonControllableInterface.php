<?php

namespace Phonedotcom\Mason\Builder;

interface MasonControllableInterface
{
    public static function getRelation();
    public static function getMasonControl($params = []);
}
