<?php

namespace Gotyefrid\ComebackpwParser\Base\Helpers;

class ObjectHelper
{
    /**
     * Сетап класса через массив
     * @param object $object
     * @param array $properties
     * @return object
     */
    public static function configure(object $object, array $properties): object
    {
        foreach ($properties as $name => $value) {
            if (property_exists($object, $name)) {
                $object->$name = $value;
            }
        }

        return $object;
    }
}