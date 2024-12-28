<?php

namespace Warkhosh\Variable;

use Exception;

class VarInt
{
    /**
     * Преобразовывает и возвращает переданное значения в целое число
     *
     * @note: результат может быть отрицательным!
     *
     * @param mixed $num
     * @param int $default
     * @param bool $strict флаг для преобразования дополнительных строковых значений типа "on|off/no|yes/false|true" в число
     * @return int
     */
    public static function getMake(
        mixed $num = null,
        int $default = 0,
        bool $strict = true
    ): int {
        if (is_null($num) || is_object($num) || is_array($num)) {
            return $default;
        } elseif (is_bool($num) || is_numeric($num) || is_float($num)) {
            return intval($num);
        }

        if (is_string($num)) {
            $num = strtolower(trim(strip_tags($num)));

            if (! $strict) {
                return match ($num) {
                    'false', 'off', 'no' => 0,
                    'true', 'on', 'yes' => 1,
                    default => intval($num),
                };
            }

            $val = intval($num);

            return $num === strval($val) ? $val : $default;
        }

        return $default;
    }

    /**
     * Преобразовывает и возвращает переданное значения в целое число
     *
     * @note: результат может быть отрицательным!
     *
     * @param mixed $num
     * @param int $default
     * @param bool $strict флаг для преобразования дополнительных значений типа "on|off|no|yes" в число
     * @return int
     * @deprecated заменить метод на VarInt::getMake
     */
    public static function getMakeInteger(
        mixed $num = null,
        int $default = 0,
        bool $strict = true
    ): int {
        return static::getMake($num, $default, $strict);
    }


    /**
     * Преобразовывает и возвращает переданное значения в целое положительное число
     *
     * @param bool|float|int|string|null $num
     * @param int $default
     * @param bool $strict флаг для преобразования дополнительных значений типа "on|off|no|yes" в число
     * @return int
     * @throws Exception
     */
    public static function getMakePositiveInteger(
        bool|float|int|string|null $num = null,
        int $default = 0,
        bool $strict = true
    ): int {
        if ($default < 0) {
            throw new Exception("The default value must be a positive integer");
        }

        if (is_null($num)) {
            $num = $default;
        } elseif (is_bool($num) || is_numeric($num)) {
            $num = intval($num);
        }

        if (is_string($num)) {
            if (! $strict) {
                return match (strtolower(trim($num))) {
                    '0', 'false', 'off', 'no' => 0,
                    '1', 'true', 'on', 'yes' => 1,
                    default => intval($num),
                };
            }

            $num = trim(strip_tags($num));
            $int = intval($num);

            $num = mb_strlen($int) === mb_strlen($num) ? $int : $default;
        }

        return $num >= 0 ? $num : $default;
    }


    /**
     * Преобразование переданного значения в число с плавающей запятой
     *
     * @param mixed $float
     * @param float $default
     * @return float
     * @throws Exception
     * @deprecated аналогична VarFloat::getMake() и поэтому этот метод подлежит удалению
     */
    public static function getMakeFloat(mixed $float = null, float $default = 0.0): float
    {
        throw new Exception("Используйте метод VarFloat::getMake()");
    }


    /**
     * Метод проверяет, а попадает ли число в диапазон и возвращает его или значение указанное по умолчанию
     *
     * @param bool|float|int|string|null $num
     * @param int $min
     * @param int $max
     * @return bool
     */
    public static function isRange(
        bool|float|int|string|null $num,
        int $min = 0,
        int $max = 1
    ): bool {
        $num = static::getMake($num);

        return ($num >= $min && $num <= $max);
    }
}
