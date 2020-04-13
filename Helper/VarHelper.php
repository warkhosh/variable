<?php

namespace Warkhosh\Variable\Helper;

class VarHelper
{
    /**
     * Быстрое преобразование значения в массив
     *
     * @param      $items
     * @param bool $strict - флаг соответствия типа
     * @return array
     */
    static public function getArrayWrap($items, $strict = true)
    {
        return $strict ? (is_array($items) ? $items : []) : (array)$items;
    }

    /**
     * Return the default value of the given value.
     *
     * @param  mixed $value
     * @return mixed
     */
    static public function value($value)
    {
        return $value instanceof \Closure ? $value() : $value;
    }

}
