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
     * @param array|bool|float|int|string|null $num
     * @param int $default
     * @param bool $strict флаг для преобразования дополнительных значений типа "on|off|no|yes" в число
     * @return int
     */
    public static function getMakeInteger(
        array|bool|float|int|string|null $num = null,
        int $default = 0,
        bool $strict = true
    ): int {
        if (is_null($num)) {
            return $default;
        } elseif (is_bool($num) || is_numeric($num) || is_float($num)) {
            return intval($num);
        }

        if (is_string($num)) {
            if (! $strict) {
                return match (strtolower(trim($num))) {
                    '0', 'false', 'off', 'no' => 0,
                    '1', 'true', 'on', 'yes' => 1,
                    default => intval($num),
                };
            }

            return intval($num);
        }

        return $default;
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
        } elseif (is_bool($num) || is_numeric($num) || is_float($num)) {
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

            $num = intval($num);
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
     * @param int $default
     * @param int $min
     * @param int $max
     * @return int
     */
    public static function getOfRange(
        bool|float|int|string|null $num,
        int $default = 0,
        int $min = 0,
        int $max = 1
    ): int {
        $num = static::getMakeInteger($num, $default);

        if ($num >= $min && $num <= $max) {
            return $num;
        }

        return $default;
    }
}
