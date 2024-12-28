<?php

declare(strict_types=1);

namespace Warkhosh\Variable\Helper;

use Closure;

class VarHelper
{
    /**
     * Быстрое преобразование значения в массив
     *
     * @param mixed $items
     * @param bool $strict - флаг соответствия типа
     * @return array
     */
    public static function getArrayWrap(mixed $items, bool $strict = true): array
    {
        return $strict ? (is_array($items) ? $items : []) : (array)$items;
    }

    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function value(mixed $value): mixed
    {
        return $value instanceof Closure ? $value() : $value;
    }

}
