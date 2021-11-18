<?php

namespace Warkhosh\Variable;

/**
 * Class VarFloat
 */
class VarFloat
{
    const TRIM_REMOVE_CHAR = " \t\n\r\0\x0B";

    /**
     * Преобразование значения в строку
     *
     * @param float|int|string $var       - значение числа
     * @param string|null      $separator - разделитель точности
     * @return string
     */
    static public function makeString($var = 0, ?string $separator = null)
    {
        $var = strval($var);

        if (is_null($separator)) {
            $systemSeparator = localeconv()['decimal_point'];

            // Русская локаль рисует разделитель десятичных как знак запятой,
            // это ломает преобразования или запись в базу, поэтому заменяем разделитель на символ точка
            if ($systemSeparator === ',' && mb_strpos($var, '.', 0, 'UTF-8') === false) {
                $var = \Warkhosh\Variable\Helper\Helper::str_replace_once($systemSeparator, '.', $var); // заменяем запятую на точку
            }

            return $var;
        }

        // Если в качестве разделителя десятичных указали точку, делаем замену в значении запятой на точку
        $badSeparator = $separator === '.' ? ',' : '.';

        return str_replace($badSeparator, $separator, $var);
    }

    /**
     * Преобразование значения в число с плавающей точкой
     *
     * @note: $round = upward, округляет десятичные значения в большую сторону если они выходят за пределы точности [$decimals]
     * @note: $round = downward, округляет десятичные значения в меньшую сторону если они выходят за пределы точности [$decimals]. В данном случае символы по правую сторону будут отрезаны.
     *
     * @param float | integer | string $var
     * @param int                      $decimals - точность
     * @param string                   $round    [auto, upward, downward] - тип округления
     * @param float                    $default
     * @return float
     */
    static public function getMake($var = 0, $decimals = 12, $round = "auto", $default = 0.0)
    {
        if (is_string($var)) {
            $separator = localeconv()['decimal_point'];
            $var = trim($var, static::TRIM_REMOVE_CHAR);

            // Русская локаль рисует разделитель десятичных как знак запятой но это ломает преобразование
            if ($separator === ',' && mb_strpos($var, '.', 0, 'UTF-8') === false) {
                $var = \Warkhosh\Variable\Helper\Helper::str_replace_once($separator, '.', $var);
            }

            $var = VarStr::getRemoveSymbol($var, [' ']);
        }

        if (is_numeric($var) || is_string($var) || is_float($var) || is_double($var) || is_bool($var)) {
            $var = floatval($var);

            if ($round === 'upward') {
                $int = "1" . str_repeat("0", $decimals);
                $var = ceil($var * $int) / $int;

            } elseif ($round === 'downward') {
                $int = "1" . str_repeat("0", $decimals);
                $var = floor($var * $int) / $int;

            } else {
                $var = round($var, $decimals);
            }

            return $var;
        }

        return floatval($default);
    }

    /**
     * Преобразование значения в число с плавающей точкой в положительном диапазоне значений
     *
     * @note: $round = upward, округляет десятичные значения в большую сторону если они выходят за пределы точности [$decimals]
     * @note: $round = downward, округляет десятичные значения в меньшую сторону если они выходят за пределы точности [$decimals]. В данном случае символы по правую сторону будут отрезаны.
     *
     * @param mixed  $var
     * @param int    $decimals - точность
     * @param string $round    [auto, upward, downward] - тип округления
     * @param float  $default
     * @return float
     */
    static public function getMakePositive($var = 0, $decimals = 12, $round = "auto", $default = 0.0)
    {
        $var = self::getMake($var, $decimals, $round, $default);
        $var = $var >= 0 ? $var : $default;

        return $var;
    }

    /**
     * Округляет число типа float
     *
     * @param float | integer | string $var
     * @param int                      $decimals
     * @param string                   $round
     * @param float                    $default
     * @return float
     */
    static public function round($var = 0.0, $decimals = 12, $round = "auto", $default = 0.0)
    {
        // для строки делаем предварительную замену альтернативного разделителя если ошиблись при вводе
        if (is_string($var)) {
            $separator = localeconv()['decimal_point'];
            $var = trim($var, static::TRIM_REMOVE_CHAR);

            // Русская локаль рисует разделитель десятичных как знак запятой но это ломает преобразование
            if ($separator === ',' && mb_strpos($var, '.', 0, 'UTF-8') === false) {
                $var = \Warkhosh\Variable\Helper\Helper::str_replace_once($separator, '.', $var);
            }

            $var = VarStr::getRemoveSymbol($var, [' ']);
        }

        if (is_numeric($var) || is_string($var) || is_float($var) || is_double($var) || is_bool($var)) {
            $var = floatval($var);

            if ($round === 'upward') {
                $int = "1" . str_repeat("0", $decimals);
                $var = ceil($var * $int) / $int;

            } elseif ($round === 'downward') {
                $int = "1" . str_repeat("0", $decimals);
                $var = floor($var * $int) / $int;

            } else {
                $var = round($var, $decimals);
            }

            return $var;
        }

        return floatval($default);
    }

    /**
     * Возвращает переданные значения, но с преобразованием под flat
     *
     * @param mixed $var
     * @return mixed
     */
    static public function getConvert($var)
    {
        if (is_string($var)) {
            $separator = localeconv()['decimal_point'];
            $str = trim($var, static::TRIM_REMOVE_CHAR);

            // руская локаль рисует разделитель десятичных как знак запятой но это ломает преобразование
            if ($separator === ',' && mb_strpos($str, '.', 0, 'UTF-8') === false) {
                $str = \Warkhosh\Variable\Helper\Helper::str_replace_once($separator, '.', $str);
            }

            $str = VarStr::getRemoveSymbol($str, [' ']);
            //$var = $str + 0;
        }

        return $var;
    }
}