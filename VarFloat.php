<?php

namespace Warkhosh\Variable;

use Exception;

/**
 * Class VarFloat
 */
class VarFloat
{
    /**
     * Преобразование значения в строку
     *
     * @param float|int|string $var значение числа
     * @param string|null $separator разделитель точности
     * @return string
     */
    public static function makeString(float|int|string $var = 0, ?string $separator = null): string
    {
        $var = strval($var);

        if (is_null($separator)) {
            $systemSeparator = localeconv()['decimal_point'];

            // Русская локаль рисует разделитель десятичных как знак запятой,
            // это ломает преобразования или запись в базу, поэтому заменяем разделитель на символ точка
            if ($systemSeparator === ',' && mb_strpos($var, '.', 0, 'UTF-8') === false) {
                $var = VarStr::replaceOnce($systemSeparator, '.', $var); // заменяем запятую на точку

            } elseif ($systemSeparator === '.' && mb_strpos($var, ',', 0, 'UTF-8') !== false) {
                $var = VarStr::replaceOnce(',', '.', $var); // заменяем запятую на точку
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
     * @param bool|float|int|string|null $var
     * @param int $decimals точность (символы после точки)
     * @param string $round тип округления (auto, upward, downward)
     * @param float $default
     * @return float
     */
    public static function getMake(
        bool|float|int|string|null $var = 0,
        int $decimals = 12,
        string $round = "auto",
        float $default = 0.0
    ): float {
        if (is_null($var) || is_numeric($var) || is_bool($var)) {
            return floatval($var);
        }

        if (is_string($var)) {
            $separator = localeconv()['decimal_point'];
            $var = VarStr::trim($var);

            // Русская локаль рисует разделитель десятичных как знак запятой, но это ломает преобразование
            if ($separator === ',' && mb_strpos($var, '.', 0, 'UTF-8') === false) {
                $var = VarStr::replaceOnce($separator, '.', $var);
            }

            $var = VarStr::getRemoveSymbol($var, [' ']);
            $var = floatval($var);

            if ($round === 'upward') {
                $int = "1".str_repeat("0", $decimals);
                $var = ceil($var * $int) / $int;

            } elseif ($round === 'downward') {
                $int = "1".str_repeat("0", $decimals);
                $var = floor($var * $int) / $int;

            } else {
                $var = round($var, $decimals);
            }

            return $var;
        }

        return $default;
    }

    /**
     * Преобразование значения в число с плавающей точкой в положительном диапазоне значений
     *
     * @note: $round = upward, округляет десятичные значения в большую сторону если они выходят за пределы точности [$decimals]
     * @note: $round = downward, округляет десятичные значения в меньшую сторону если они выходят за пределы точности [$decimals]. В данном случае символы по правую сторону будут отрезаны.
     *
     * @param bool|float|int|string|null $var
     * @param int $decimals точность (символы после точки)
     * @param string $round тип округления (auto, upward, downward)
     * @param float $default
     * @return float
     */
    public static function getMakePositive(
        bool|float|int|string|null $var = 0,
        int $decimals = 12,
        string $round = "auto",
        float $default = 0.0
    ): float {
        $var = self::getMake($var, $decimals, $round, $default);

        return $var >= 0 ? $var : $default;
    }

    /**
     * Округляет число типа float
     *
     * @param bool|float|int|string|null $var
     * @param int $decimals точность (символы после точки)
     * @param string $round
     * @param float $default
     * @return float
     */
    public static function round(
        bool|float|int|string|null $var = 0.0,
        int $decimals = 12,
        string $round = "auto",
        float $default = 0.0
    ): float {
        if (is_null($var) || is_numeric($var) || is_bool($var)) {
            return floatval($var);
        }

        // для строки делаем предварительную замену альтернативного разделителя если ошиблись при вводе
        if (is_string($var)) {
            $separator = localeconv()['decimal_point'];
            $var = VarStr::trim($var);

            // Русская локаль рисует разделитель десятичных как знак запятой, но это ломает преобразование
            if ($separator === ',' && mb_strpos($var, '.', 0, 'UTF-8') === false) {
                $var = VarStr::replaceOnce($separator, '.', $var);
            }

            $var = VarStr::getRemoveSymbol($var, [' ']);
            $var = floatval($var);

            if ($round === 'upward') {
                $int = "1".str_repeat("0", $decimals);
                $var = ceil($var * $int) / $int;

            } elseif ($round === 'downward') {
                $int = "1".str_repeat("0", $decimals);
                $var = floor($var * $int) / $int;

            } else {
                $var = round($var, $decimals);
            }

            return $var;
        }

        return $default;
    }

    /**
     * Возвращает результат мягкой проверки значения на float для последующей конвертации без ошибок
     *
     * @note в основном это проверка для строк, и не стоит перед этим методом пренебрегать проверками типа: is_null, is_array...
     *
     * @param mixed $data
     * @return bool
     * @example echo VarFloat::isStringOnFloat($price) ? VarFloat::getMake($price) : 0.0
     */
    public static function isStringOnFloat(mixed $data): bool
    {
        if (is_float($data)) {
            return true;
        }

        if (is_string($data)) {
            $separator = localeconv()['decimal_point'];

            // Русская локаль рисует разделитель десятичных как знак запятой, но это ломает преобразование
            if ($separator === ',' && mb_strpos($data, '.', 0, 'UTF-8') === false) {
                $data = VarStr::replaceOnce($separator, '.', $data);
            }

            if (is_numeric($data) && is_float($data + 0)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Возвращает переданные значения, но с преобразованием под flat
     *
     * @param mixed $var
     * @return mixed
     * @throws Exception
     * @deprecated аналогична getMake() и подлежит удалению
     */
    public static function getConvert(mixed $var): mixed
    {
        throw new Exception("Используйте метод VarFloat::getMake()");
    }
}
