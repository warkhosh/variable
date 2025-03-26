<?php

declare(strict_types=1);

namespace Warkhosh\Variable;

use Exception;

/**
 * Class VarFloat
 */
class VarFloat
{
    /**
     * Символ разделителя значения десятичных чисел в проекте
     */
    public const SEPARATOR = '.';

    /**
     * Преобразование значения в число с плавающей точкой
     *
     * @note: $round = upward, округляет десятичные значения в большую сторону если они выходят за пределы точности [$decimals]
     * @note: $round = downward, округляет десятичные значения в меньшую сторону если они выходят за пределы точности [$decimals].
     *        В данном случае символы по правую сторону будут отрезаны.
     *
     * @param mixed $var
     * @param int $decimals точность (символы после точки)
     * @param string $round тип округления (auto, upward, downward)
     * @param float $default
     * @return float
     */
    public static function getMake(
        mixed $var = 0,
        int $decimals = 1,
        string $round = "auto",
        float $default = 0.0
    ): float {
        return getMakeFloat($var, $decimals, $round, $default, static::SEPARATOR);
        //if (is_null($var) || (is_string($var) && trim($var) === '') || is_array($var)) {
        //    return floatval("0.".str_repeat("0", $decimals));
        //}
        //
        //if (is_bool($var)) {
        //    return floatval(VarStr::getNumberFormat((int)$var, $decimals, static::SEPARATOR));
        //}
        //
        //// Строка, которая не прошла проверку is_numeric()
        //if (! is_numeric($var) && is_string($var)) {
        //    $var = trim($var);
        //
        //    // Строка проходит проверку десятичное число
        //    if (is_numeric($var) && is_float($var + 0)) {
        //        // Округляем число согласно указанным значениям
        //        $var = static::rounding((float)$var, $decimals, $round);
        //
        //        // Дополняем точность числа и возвращаем
        //        return (float)number_format($var, $decimals, static::SEPARATOR);
        //    }
        //
        //    // Если в проекте символ разделителя десятичных чисел точка,
        //    // далее идёт проверка и перевод строки как десятичного числа из русской локали в международную (с точкой)
        //    if (static::SEPARATOR === ".") {
        //        $string = preg_replace('/[^\d,]/ium', '', $var);
        //
        //        // В строке не найдены лишние символы, но присутствует символ запятой
        //        if ($string === $var && mb_strpos($var, ',', 0, 'UTF-8') > 0) {
        //            $var = VarStr::replaceFirst(',', static::SEPARATOR, $var);
        //        }
        //    }
        //
        //    // Если строка после вышестоящих логик все равно не проходит проверку на число с плавающей точкой
        //    if (! (is_numeric($var) && is_float($var + 0))) {
        //        $var = $default;
        //    }
        //}
        //
        //// Если указанное значение проходит проверку на число (int/float) или вышестоящий алгоритм преобразовал строку в числовую
        //if (is_numeric($var)) {
        //    if (is_integer($var)) {
        //        return floatval("{$var}.".str_repeat("0", $decimals));
        //    }
        //
        //    // Округляем число согласно указанным значениям
        //    $var = static::rounding((float)$var, $decimals, $round);
        //
        //    // Дополняем точность числа и возвращаем
        //    return (float)number_format($var, $decimals, static::SEPARATOR, '');
        //}
        //
        //// Округляем default число согласно указанным значениям
        //$var = static::rounding($default, $decimals, $round);
        //
        //// Дополняем точность числа и возвращаем
        //return (float)number_format($var, $decimals, static::SEPARATOR, '');
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
     * @throws Exception
     */
    public static function getMakePositive(
        bool|float|int|string|null $var = 0,
        int $decimals = 1,
        string $round = "auto",
        float $default = 0.0
    ): float {
        $var = self::getMake($var, $decimals, $round, $default);

        return $var >= 0 ? $var : floatval(VarStr::getNumberFormat($default, $decimals, static::SEPARATOR));
    }

    /**
     * Универсальный метод округления числа типа float
     *
     * @param bool|float|int|string|null $var
     * @param int $decimals точность (символы после точки)
     * @param string $round
     * @param float $default
     * @return float
     * @throws Exception
     * @deprecated поменять на VarFloat::getRound(...)
     */
    public static function round(
        bool|float|int|string|null $var = 0.0,
        int $decimals = 1,
        string $round = "auto",
        float $default = 0.0
    ): float {
        throw new Exception("Используйте метод VarFloat::getRound()");
    }

    /**
     * Универсальный метод округления числа типа float
     *
     * @param bool|float|int|string|null $var
     * @param int $decimals точность (символы после точки)
     * @param string $round
     * @param float $default
     * @return float
     * @throws Exception
     */
    public static function getRound(
        bool|float|int|string|null $var = 0.0,
        int $decimals = 1,
        string $round = "auto",
        float $default = 0.0
    ): float {
        return self::getMake($var, $decimals, $round, $default);
    }

    /**
     * Универсальный метод преобразования float значения в строку
     *
     * @param float|int|string|null $var значение числа
     * @param int $decimals точность (символы после точки)
     * @param string $round
     * @return string
     * @throws Exception
     * @deprecated поменять на VarFloat::getString(...)
     */
    public static function makeString(float|int|string|null $var = 0, int $decimals = 1, string $round = "auto"): string
    {
        throw new Exception("Используйте метод VarFloat::getString()");
    }

    /**
     * Универсальный метод преобразования float значения в строку
     *
     * @param float|int|string|null $var значение числа
     * @param int $decimals точность (символы после точки)
     * @param string $round
     * @return string
     * @throws Exception
     */
    public static function getString(float|int|string|null $var = 0, int $decimals = 1, string $round = "auto"): string
    {
        $str = (string)static::getMake($var, $decimals, $round);

        if ($decimals > 0) {
            if (VarStr::find('.')) {
                [$number, $decimal] = explode('.', $str);
                $quantity = VarStr::length($decimal) - $decimals;
                $decimal = $decimal.($quantity > 0 ? str_repeat("0", $quantity) : "");

                return "{$number}.{$decimal}";
            } else {
                $decimal = str_repeat("0", $decimals);
                return "{$str}.{$decimal}";
            }
        }

        return $str;
    }

    /**
     * Возвращает результат мягкой проверки значения на float, но исключительно в контексте типа string/float
     *
     * @note В основном это проверка для строк, и не стоит перед этим методом пренебрегать проверками типа: is_null, is_array...
     *       Тип принимаемых значений mixed указан для того что-бы не вызывать исключение неверного типа если передадут число или null.
     *
     * @param mixed $str
     * @return bool
     * @example echo VarFloat::isStringOnFloat($price) ? VarFloat::getMake($price) : 0.0
     */
    public static function isStringOnFloat(mixed $str): bool
    {
        if (is_float($str)) {
            return true;
        }

        if (is_string($str) && ! empty($str)) {
            $str = trim($str);

            // Строка проходит проверку десятичное число
            if (is_numeric($str) && is_float($str + 0)) {
                return true;
            }

            // Далее идёт проверка строки как десятичного числа в русской локали
            $string = preg_replace('/[^\d,]/ium', '', $str);

            // В строке не найдены лишние символы, но присутствует символ запятой
            if ($string === $str && mb_strpos($str, ',', 0, 'UTF-8') > 0) {
                $str = VarStr::replaceFirst(',', static::SEPARATOR, $str);
            }

            if (is_numeric($str) && is_float($str + 0)) {
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

    /**
     * Округляет десятичное число
     *
     * @param float $var
     * @param int $decimals
     * @param string $round
     * @return float
     */
    protected static function rounding(float $var, int $decimals = 12, string $round = "auto"): float
    {
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
}
