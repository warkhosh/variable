<?php

namespace Warkhosh\Variable\Traits;

use Throwable;
use Warkhosh\Variable\VarDateTime;
use Warkhosh\Variable\VarFloat;
use Warkhosh\Variable\VarStr;
use Exception;

/**
 * Trait VariableExtendedMethod
 *
 * @package Ekv\Framework\Components\Support\Traits
 */
trait VariableExtendedMethod
{
    /**
     * Удаление значений из данных
     *
     * @param array $remove ['', 0,  '0', null, 'null']
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     * @throws Exception
     */
    public function removeItems(array $remove = ['', 0, '0', null, 'null'], bool $recursive = false): static
    {
        $default = $this->getDefault();
        $this->data = static::getRemoveItems($this->data, $remove, $default, $recursive);

        return $this;
    }

    /**
     * Удаление значений из данных
     *
     * @param array|bool|float|int|string|null $data
     * @param array $remove ['', 0, '0', null, 'null']
     * @param array|bool|float|int|string|null $default
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     * @throws Exception
     */
    public static function getRemoveItems(
        float|array|bool|int|string|null $data,
        array $remove = ['', 0, '0', null, 'null'],
        float|array|bool|int|string|null $default = null,
        bool $recursive = false
    ): array|string {
        if (is_string($data)) {
            foreach ($remove as $rem) {
                if ($data === $rem) {
                    $data = $default;
                }
            }
        }

        if (is_array($data) && count($data) > 0) {
            reset($data);

            foreach ($data as $key => $item) {
                if ($recursive && is_array($item)) {
                    $data[$key] = static::getRemoveItems($item, $remove, $default, $recursive);
                } else {
                    if (in_array($item, $remove)) {
                        $data[$key] = null;
                        unset($data[$key]);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Преобразование значения(й) в целое число с проверкой, что они выше или равны нулю
     *
     * @note этот метод проверяет на минимальное значение из указанного числа по умолчанию!
     * @note возможны отрицательные значения!
     *
     * @param bool $recursive флаг для обхода потомков
     * @return array|int
     * @throws Exception
     */
    public function getIntegerNotLess(bool $recursive = false): array|int
    {
        return $this->integerNotLess($recursive)->get();
    }

    /**
     * Преобразование значения(й) в целое число с проверкой, что они выше или равны нулю
     *
     * @note этот метод проверяет на минимальное значение из указанного числа по умолчанию!
     * @note возможны отрицательные значения!
     *
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     * @throws Exception
     */
    public function integerNotLess(bool $recursive = false): static
    {
        $default = $this->getDefault();

        if (! (is_null($default) || is_numeric($default))) {
            throw new Exception("Default values are not an number");
        }

        $this->data = static::getMinInteger($this->data, 0, (int)$default, $recursive);

        return $this;
    }

    /**
     * Преобразование значения(й) в положительную цену для товара с типом строка
     *
     * @param string $round тип округления (auto, upward, downward)
     * @param int $decimals точность (символы после точки)
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     * @throws Exception
     */
    public function stringWithGreaterZero(string $round = "auto", int $decimals = 12, bool $recursive = false): static
    {
        $default = $this->getDefault();

        if (! (is_null($default) || is_string($default) || is_numeric($default))) {
            throw new Exception("Default values are not an string");
        }

        $this->data = static::getStringWithGreaterZero($this->data, $round, $decimals, (string)$default, $recursive);

        return $this;
    }

    /**
     * Возвращает преобразованное значение(я) в положительную цену для товара с типом строка
     *
     * @note decimals тут специально передают точность ($decimals), что-бы сразу преобразовать значения, а то number_format может по своему округлить!
     *
     * @param array|float|int|string|null $data
     * @param string $round тип округления десятичного значения (auto, upward, downward)
     * @param int $decimals точность (символы после точки)
     * @param string|null $default
     * @param bool $recursive
     * @return array|string
     * @throws Exception
     */
    public static function getStringWithGreaterZero(
        array|float|int|string|null $data,
        string $round = "auto",
        int $decimals = 12,
        ?string $default = null,
        bool $recursive = false
    ): array|string {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getStringWithGreaterZero($item, $default, $recursive);

                    } else {
                        $number = is_numeric($item) ? $item : (string)$default;
                        $number = VarFloat::isStringOnFloat($number)
                            ? VarFloat::getMakePositive($number, $decimals, $round)
                            : intval($number);
                        $return[$key] = strval($number >= 0 ? VarFloat::getString($number, 2) : $default);
                    }
                }
            }

        } else {
            $number = is_numeric($data) ? $data : (string)$default;
            $number = VarFloat::isStringOnFloat($number)
                ? VarFloat::getMakePositive($number, $decimals, $round)
                : intval($number);
            $return = strval($number >= 0 ? VarFloat::getString($number, 2) : $default);
        }

        return $return;
    }

    /**
     * Заменяет все вхождения строки поиска на строку замены
     *
     * @param array|string $search искомое значение
     * @param array|string $replace значение замены
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     */
    public function strReplace(array|string $search = [], array|string $replace = [], bool $recursive = false): static
    {
        $this->data = static::getStrReplace($this->data, $search, $replace, $recursive);

        return $this;
    }

    /**
     * Заменяет все вхождения строки поиска на строку замены
     *
     * @param array|bool|float|int|string|null $data
     * @param array|string $search искомое значение
     * @param array|string $replace значение замены
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     */
    public static function getStrReplace(
        array|bool|float|int|string|null $data,
        array|string $search = [],
        array|string $replace = [],
        bool $recursive = false
    ): array|string {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getStrReplace($item, $search, $replace, $recursive);

                    } else {
                        $item = is_string($item) ? $item : VarStr::getMake($item);
                        $return[$key] = str_replace($search, $replace, $item);
                    }
                }
            }

        } else {
            $data = is_string($data) ? $data : VarStr::getMake($data);
            $return = str_replace($search, $replace, $data);
        }

        return $return;
    }

    /**
     * Вставляет HTML-код разрыва строки перед каждым переводом строки
     *
     * @param bool $is_xhtml
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     */
    public function nl2br(bool $is_xhtml = false, bool $recursive = false): static
    {
        $this->data = static::getNl2br($this->data, $is_xhtml, $recursive);

        return $this;
    }

    /**
     * Вставляет HTML-код разрыва строки перед каждым переводом строки
     *
     * @param array|bool|float|int|string|null $data
     * @param bool $is_xhtml
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     */
    public static function getNl2br(
        array|bool|float|int|string|null $data,
        bool $is_xhtml = false,
        bool $recursive = false
    ): array|string {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getNl2br($item, $is_xhtml, $recursive);

                    } else {
                        $item = is_string($item) ? $item : VarStr::getMake($item);
                        $return[$key] = nl2br($item, $is_xhtml);
                    }
                }
            }

        } else {
            $data = is_string($data) ? $data : VarStr::getMake($data);
            $return = nl2br($data, $is_xhtml);
        }

        return $return;
    }

    /**
     * Удаляет HTML и PHP-теги из данных
     *
     * @param string $allowable_tags
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     */
    public function stripTags(string $allowable_tags = '', bool $recursive = false): static
    {
        $this->data = static::getStripTags($this->data, $allowable_tags, $recursive);

        return $this;
    }

    /**
     * Удаляет HTML и PHP-теги из данных
     *
     * @param array|bool|float|int|string|null $data
     * @param string $allowable_tags
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     */
    public static function getStripTags(
        array|bool|float|int|string|null $data,
        string $allowable_tags = '',
        bool $recursive = false
    ): array|string {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getStripTags($item, $allowable_tags, $recursive);

                    } else {
                        $item = is_string($item) ? $item : VarStr::getMake($item);
                        $return[$key] = strip_tags($item, $allowable_tags);
                    }
                }
            }
        } else {
            $data = is_string($data) ? $data : VarStr::getMake($data);
            $return = strip_tags($data, $allowable_tags);
        }

        return $return;
    }

    /**
     * Преобразование значений в MD5-хэш
     *
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     */
    public function makeMd5(bool $recursive = false): static
    {
        $this->data = static::getMd5($this->data, $recursive);

        return $this;
    }

    /**
     * Преобразование значений в MD5-хэш
     *
     * @param array|bool|float|int|string|null $data
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     */
    public static function getMd5(array|bool|float|int|string|null $data, bool $recursive = false): array|string
    {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getMd5($item, $recursive);

                    } else {
                        $return[$key] = md5(is_string($item) ? $item : VarStr::getMake($item));
                    }
                }
            }
        } else {
            $return = md5(VarStr::getMake($data));
        }

        return $return;
    }

    /**
     * Преобразовывает строку(и) в нижний регистр (lower-case)
     *
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     * @throws Exception
     */
    public function lower(bool $recursive = false): static
    {
        $this->data = static::getLower($this->data, $recursive);

        return $this;
    }

    /**
     * Преобразовывает строку(и) в нижний регистр (lower-case)
     *
     * @param array|bool|float|int|string|null $data
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     * @throws Exception
     */
    public static function getLower(array|bool|float|int|string|null $data, bool $recursive = false): array|string
    {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getLower($item, $recursive);

                    } else {
                        $item = is_string($item) ? $item : VarStr::getMake($item);
                        $return[$key] = VarStr::getLower($item);
                    }
                }
            }

        } else {
            $data = is_string($data) ? $data : VarStr::getMake($data);
            $return = VarStr::getLower($data);
        }

        return $return;
    }

    /**
     * Преобразовывает строку(и) в верхний регистр (upper-case)
     *
     * @param bool $recursive - флаг для обхода потомков
     * @return $this
     * @throws Exception
     */
    public function upper(bool $recursive = false): static
    {
        $this->data = static::getUpper($this->data, $recursive);

        return $this;
    }

    /**
     * Преобразовывает строку(и) в верхний регистр (upper-case)
     *
     * @param array|bool|float|int|string|null $data
     * @param bool $recursive - флаг для обхода потомков
     * @return array|string
     * @throws Exception
     */
    public static function getUpper(array|bool|float|int|string|null $data, bool $recursive = false): array|string
    {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getUpper($item, $recursive);

                    } else {
                        $item = is_string($item) ? $item : VarStr::getMake($item);
                        $return[$key] = VarStr::getUpper($item);
                    }
                }
            }

        } else {
            $data = is_string($data) ? $data : VarStr::getMake($data);
            $return = VarStr::getUpper($data);
        }

        return $return;
    }

    /**
     * Экранирует строку с помощью слешей
     *
     * @note Экранируются следующие символы: одинарная кавычка ['], двойная кавычка ["], обратный слеш [\], NUL (байт NULL)
     *
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     */
    public function addSlashes(bool $recursive = false): static
    {
        $this->data = static::getAddSlashes($this->data, $recursive);

        return $this;
    }

    /**
     * Экранирует строку с помощью слешей
     *
     * @note Экранируются следующие символы: одинарная кавычка ['], двойная кавычка ["], обратный слеш [\], NUL (байт NULL)
     *
     * @param array|bool|float|int|string|null $data
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     */
    public static function getAddSlashes(array|bool|float|int|string|null $data, bool $recursive = false): array|string
    {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getAddSlashes($item, $recursive);

                    } else {
                        $item = is_string($item) ? $item : VarStr::getMake($item);
                        $return[$key] = addslashes($item);
                    }
                }
            }
        } else {
            $data = is_string($data) ? $data : VarStr::getMake($data);
            $return = addslashes($data);
        }

        return $return;
    }

    /**
     * Удаляет экранирование символов
     *
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     */
    public function stripSlashes(bool $recursive = false): static
    {
        $this->data = static::getStripSlashes($this->data, $recursive);

        return $this;
    }

    /**
     * Удаляет экранирование символов
     *
     * @param array|bool|float|int|string|null $data
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     */
    public static function getStripSlashes(
        array|bool|float|int|string|null $data,
        bool $recursive = false
    ): array|string {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getStripSlashes($item, $recursive);

                    } else {
                        $item = is_string($item) ? $item : VarStr::getMake($item);
                        $return[$key] = stripslashes($item);
                    }
                }
            }

        } else {
            $data = is_string($data) ? $data : VarStr::getMake($data);
            $return = stripslashes($data);
        }

        return $return;
    }

    /**
     * Перевод данных в дату с проверкой по формату
     *
     * @param string $format
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     * @throws Exception
     */
    public function makeDate(string $format = 'Y-m-d', bool $recursive = false): static
    {
        $this->data = static::getMakeDate($this->data, $this->getDefault(), $format, $recursive);

        return $this;
    }

    /**
     * Проверка даты по указанному формату
     *
     * @note пустые значения будут заполнены значениями из $default!
     *
     * @param array|bool|float|int|string|null $data
     * @param string $default
     * @param string $format
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     * @throws Exception
     */
    public static function getMakeDate(
        array|bool|float|int|string|null $data,
        string $default,
        string $format = 'Y-m-d',
        bool $recursive = false
    ): array|string {
        if (is_array($data) && is_array($return = [])) {

            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getMakeDate($item, $default, $format, $recursive);

                    } else {
                        $item = trim(is_string($item) ? $item : VarStr::getMake($item));

                        try {
                            $item = VarDateTime::getConvertDateTime($item, $format);
                            $return[$key] = $item !== "" ? $item : $default;
                        } catch (Throwable) {
                            $return[$key] = $default;
                        }
                    }
                }
            }

        } else {
            $data = trim(is_string($data) ? $data : VarStr::getMake($data));

            try {
                $data = VarDateTime::getConvertDateTime($data, $format);
                $return = $data !== "" ? $data : $default;
            } catch (Throwable) {
                $return = $default;
            }
        }

        return $return;
    }

    /**
     * Возвращает значение(я) которое были проверены через $needle (как in_array), но только тут ещё значения default
     *
     * @param array $needle
     * @param bool $strict
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     * @throws Exception
     */
    public function inArray(array $needle = [], bool $strict = false, bool $recursive = false): static
    {
        $this->data = static::getInArray($this->data, $this->getDefault(), $needle, $strict, $recursive);

        return $this;
    }

    /**
     * Возвращает значение(я) которое были проверены через $needle (как in_array), но только тут ещё значения default
     *
     * @param array|bool|float|int|string|null $data
     * @param string $default
     * @param array $needle
     * @param bool $strict
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     * @throws Exception
     */
    public static function getInArray(
        array|bool|float|int|string|null $data,
        string $default,
        array $needle = [],
        bool $strict = false,
        bool $recursive = false
    ): array|string {
        if (is_array($data)) {
            throw new Exception("Для массива поведение не предусмотрено!");
        }

        $data = static::getTrim(VarStr::getMake($data));

        return in_array($data, $needle, $strict) ? $data : $default;
    }
}
