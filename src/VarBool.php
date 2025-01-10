<?php

namespace Warkhosh\Variable;

class VarBool
{
    /**
     * Преобразовывает и возвращает переданное значения в логическое значение
     *
     * @param mixed $bool
     * @param bool $default
     * @param bool $strict флаг для преобразования дополнительных строковых значений типа "1|0/on|off/yes|no" в логическое значение
     * @return bool
     */
    public static function getMake(
        mixed $bool = null,
        bool $default = false,
        bool $strict = true
    ): bool {
        if (is_null($bool) || is_object($bool) || is_array($bool)) {
            return $default;
        } elseif (is_bool($bool) || is_numeric($bool) || is_float($bool)) {
            return boolval($bool);
        }

        if (is_string($bool)) {
            $bool = mb_strtolower(trim(strip_tags($bool)));

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

    /**
     * Проверка истинности значения
     *
     * @param mixed $var
     * @param bool $strict
     * @return bool
     */
    public static function isTrue(mixed $var = null, bool $strict = false): bool
    {
        if ($var === true) {
            return true;
        }

        if (is_array($var) || is_object($var)) {
            return false;
        }

        if ($strict === false) {
            if ((int)$var === 1 || mb_strtolower(trim((string)$var)) === 'true') {
                return true;
            }
        }

        return false;
    }

    /**
     * Проверка истинности значения
     *
     * @param mixed $var
     * @param bool $strict
     * @return bool
     */
    public static function isFalse(mixed $var = null, bool $strict = false): bool
    {
        if ($var === false) {
            return true;
        }

        if (is_array($var) || is_object($var)) {
            return false;
        }

        if ($strict === false) {
            if (((int)$var === 0 || mb_strtolower(trim((string)$var)) === 'false')) {
                return true;
            }
        }

        return false;
    }
}
