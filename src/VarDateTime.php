<?php

declare(strict_types=1);

namespace Warkhosh\Variable;

use DateTime;
use Exception;

/**
 * Class VarDateTime
 *
 * Класс по работе датами и временем
 *
 * @package Warkhosh\Variable
 */
class VarDateTime
{
    /**
     * Проверка даты под указанный формат
     *
     * @param float|int|string|null $str
     * @param string $format
     * @return bool
     */
    public static function validateDateTime(float|int|string|null $str = null, string $format = 'Y-m-d H:i:s'): bool
    {
        if (! is_string($str)) {
            return false;
        }

        $str = VarStr::crop($str, 50);
        $dt = DateTime::createFromFormat($format, $str);

        return $dt && $dt->format($format) === $str;
    }

    /**
     * Возвращает строку или число, преобразованную в дату по указанному формату
     *
     * @note метод в первую очередь для работы со строками, а числа всегда можно преобразовать в любой формат!
     *
     * @param float|int|string|null $str значение даты может быть как строка или timestamp
     * @param string $format
     * @param string|null $default
     * @return string|null
     * @throws Exception
     */
    public static function getConvertDateTime(
        float|int|string|null $str,
        string $format = 'Y-m-d',
        ?string $default = null
    ): string|null {
        if (is_null($str)
            || (is_string($str) && trim($str) === '')
            || is_float($str)
            || (is_numeric($str) && $str <= 0)
        ) {
            return $default;
        }

        if (is_string($str)) {
            $date = VarStr::trim($str);
            $date = VarStr::crop($date, 50);
            $dateTime = DateTime::createFromFormat($format, $date);

            return $dateTime ? $dateTime->format($format) : $default;
        }

        if (is_int($str)) {
            return date($format, $str);
        }

        return $default;
    }

    /**
     * Преобразует строку в дату по указанному формату
     *
     * @note учитывайте что преобразование дат нужно для строк, поэтому тут не указан тип int в $str, поскольку этот метод не возвращает значения!
     *
     * @param string|null $str
     * @param string $format
     * @param string|null $default
     * @return void
     * @throws Exception
     */
    public static function makeDateTime(?string &$str, string $format = 'Y-m-d', ?string $default = null): void
    {
        if (is_null($str) || isEmptyString($str)) {
            $str = $default;

            return;
        }

        $date = VarStr::trim($str);
        $date = mb_substr($date, 0, 50, VarStr::ENCODING);

        $dt = DateTime::createFromFormat($format, $date);

        if (! $dt) {
            throw new Exception(
                "An error occurred when converting the date to the specified format"
            );
        }

        $str = $dt->format($format);
    }

    /**
     * Преобразует переданную дату в формат timestamp
     *
     * @param int|string|null $str
     * @return int
     * @throws Exception
     */
    public static function getTimestamp(int|string|null $str): int
    {
        return (new DateTime(empty($str) ? 'now' : $str))->getTimestamp();
    }
}
