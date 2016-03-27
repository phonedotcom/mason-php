<?php

namespace PhoneCom\Mason\Builder;

interface MasonControllableInterface
{
    public static function getRelation();
    public static function getMasonControl($params = []);
}
