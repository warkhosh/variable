<?php

namespace Warkhosh\Variable;

class VarInt
{
    /**
     * Преобразование переданного значения в целое число!
     *
     * @note: результат может быть отрицательным!
     *
     * @param mixed $int
     * @param int   $default
     * @param bool  $strict - флаг для преобразования дополнительных значений типа "on|off|no|yes" в число
     * @return int
     */
    static public function getMakeInteger($int = null, $default = 0, $strict = true)
    {
        if (is_numeric($int) || is_string($int) || is_float($int) || is_double($int) || is_bool($int)) {
            if (! $strict) {
                switch (strtolower(trim($int))) {
                    case '1':
                    case 'true':
                    case 'on':
                    case 'yes':
                        return 1;
                        break;

                    case '0':
                    case 'false':
                    case 'off':
                    case 'no':
                        return 0;
                        break;
                }
            }

            return  intval($int);
        }

        return intval($default);
    }


    /**
     * Возращает целое и положительное число если такое передали, иначе вернет второе значение ( по умолчанию )
     *
     * @param mixed $num
     * @param int   $default
     * @param bool  $strict - флаг для преобразования дополнительных значений типа "on|off|no|yes" в число
     * @return int
     */
    static public function getMakePositiveInteger($num = null, $default = 0, $strict = true)
    {
        if (is_numeric($num) || is_string($num) || is_float($num) || is_double($num) || is_bool($num)) {
            if (! $strict) {
                switch (strtolower(trim($num))) {
                    case '1':
                    case 'true':
                    case 'on':
                    case 'yes':
                        return 1;
                        break;

                    case '0':
                    case 'false':
                    case 'off':
                    case 'no':
                        return 0;
                        break;
                }
            }

            $num = intval($num);
            return $num >= 0 ? $num : $default;
        }

        return intval($default);
    }


    /**
     * Преобразование переданного значения в число с плавающей запятой
     *
     * @note: результат может быть отрицательным!
     *
     * @param null  $float
     * @param float $default
     * @return float
     */
    static public function getMakeFloat($float = null, $default = 0.0)
    {
        if (is_float($float + 0)) {
            return (float)$float;
        }

        return $default;
    }


    /**
     * Метод проверяет попадает ли число в диапазон и возращает его или значение указаное по умолчанию
     *
     * @param integer $num
     * @param integer $default
     * @param integer $min
     * @param integer $max
     * @return integer
     */
    static public function getOfRange($num, $default = 0, $min = 0, $max = 1)
    {
        $num = static::getMakeInteger($num, $default);

        if ($num >= $min && $num <= $max) {
            return $num;
        }

        return $default;
    }
}