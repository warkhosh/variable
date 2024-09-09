<?php

namespace Warkhosh\Variable;

class VarBool
{
    /**
     * Преобразовывает и возвращает переданное значения в логическое значение
     *
     * @param array|bool|float|int|string|null $bool
     * @param bool $default
     * @param bool $strict флаг для преобразования дополнительных строковых значений типа "1|0/on|off/yes|no" в логическое значение
     * @return int
     */
    public static function getMake(
        mixed $bool = null,
        bool $default = false,
        bool $strict = true
    ): int {
        if (is_null($bool)) {
            return $default;
        } elseif (is_bool($bool) || is_numeric($bool) || is_float($bool)) {
            return boolval($bool);
        }

        if (is_string($bool)) {
            $bool = strtolower(trim(strip_tags($bool)));

            if (! $strict) {
                return match ($bool) {
                    '0', 'off', 'no' => false,
                    '1', 'on', 'yes' => true,
                    default => boolval($bool),
                };
            }

            $val = boolval($bool);

            return $bool === strval($val) ? $val : $default;
        }

        return $default;
    }
}
