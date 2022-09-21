<?php

namespace Warkhosh\Variable\Traits;

use Warkhosh\Variable\Helper\Helper;
use Warkhosh\Variable\VarArray;
use Warkhosh\Variable\VarFloat;
use Warkhosh\Variable\VariableOptions;
use Warkhosh\Variable\VarInt;
use Warkhosh\Variable\VarStr;

/**
 * Trait VariableMethod
 *
 * $data - Это база значения переменной или переменных.
 * Если в начале преобразовать эту переменную в нужный тип ( массив | строку )
 * последующие методы будет автоматически выполнять под этот тип алгоритмы!
 * Так мы сразу определяем тип значения переменной ( массив или все остальное )
 *
 * @method static $this array(string $delimiter = null)
 * @method static $this string(string $delimiter = null)
 *
 * @package Ekv\Framework\Components\Support\Traits
 */
trait VariableMethod
{
    /**
     * @var array | string | integer | float - $data
     */
    protected $data;

    /**
     * Default значение
     *
     * @note надо следить что-бы в значение по умолчанию не передавали массив или объект ( если этого не требует логика )
     *
     * @var array | string | integer | float | null $default
     */
    protected $default;

    /**
     * Для замены пустых строк при отдаче значений
     *
     * @var string
     */
    protected $convertAnEmptyString = '';

    /**
     * Значение сценария по которому происходит преобразование значения
     *
     * @var string
     */
    protected $__option;

    /**
     * Записываем параметр для использования их как Default;
     *
     * @param mixed $default
     * @return $this;
     */
    public function byDefault($default)
    {
        $this->default = $default;

        return $this;
    }


    /**
     * Возвращает значения по умолчанию
     *
     * @param null $key
     * @return mixed
     */
    public function getDefault($key = null)
    {
        if (! is_null($key)) {
            if (isset($this->default[$key]) && array_key_exists($key, $this->default)) {
                return $this->default[$key];
            }

            return null;
        }

        return $this->default;
    }


    /**
     * Объявление типа переменной с преобразованием в неё если она не такая.
     *
     * @param string $type
     * @param null   $arguments
     */
    public function setType($type = 'data', $arguments = null)
    {
        if ($type === 'array') {
            $this->dataType = 'array'; // для определения как отдавать значения

            if (! is_array($this->data)) {
                // что-бы преобразование из текста сработало и первым элементом массива стал полученый текст,
                // нужен не пустой разделитель. Делаем разделитель запятая по умолчанию
                $delimiter = isset($arguments[0]) ? $arguments[0] : ',';
                $this->toArray($delimiter);
            }
        }

        if ($type === 'string' || $type === 'integer') {
            $this->dataType = 'value'; // для определения как отдавать значения

            if (is_array($this->data)) {
                $delimiter = isset($arguments[0]) ? $arguments[0] : ',';
                $this->toString($this->data, $delimiter);
            }
        }

        if ($type === 'float' || $type === 'double') {
            $this->dataType = 'value'; // для определения как отдавать значения

            if (is_array($this->data)) {
                $this->data = VarArray::getFirst($this->data);
            }

            $this->data = floatval($this->data);
        }
    }


    /**
     * Магический метод для реализации вызова зарезервированных методов как функций
     *
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        switch ($name) {
            case "array":
            case "string":
            case "integer":
            case "float":
            case "double":
                $this->setType($name, $arguments);

                return $this;
        }

        //Log::error('Call of unknown method name: ' . $name . " in class: " . get_called_class());
    }


    /**
     * Преобразование значения переменной в тип = массив!
     * Для последующих операций над переменной именно по сценарию массивов что-бы вернуть массив.
     *
     * @note: Если передать третий параметр то это будет значением по умолчанию!
     *
     * @param string|null $delimiter
     * @return $this
     */
    public function toArray($delimiter = null)
    {
        $default = count(func_get_args()) === 2 ? func_get_arg(1) : $this->getDefault();

        if (gettype($this->data) == 'string') {
            $this->data = static::getToArray($this->data, $delimiter, $default);

        } else {
            $this->data = (array)$this->data;
        }

        return $this;
    }


    /**
     * Преобразование значения переменной в тип = массив!
     * Для последующих операций над переменной именно по сценарию массивов что-бы вернуть массив.
     *
     * @param array|int   $data
     * @param string|null $delimiter
     * @param array       $default
     * @return array
     */
    static public function getToArray($data, $delimiter = null, $default = [])
    {
        $return = [$default];

        if (! is_array($data)) {

            if (is_string($delimiter) && mb_strlen($delimiter) > 0) {
                if (mb_strlen($data = (gettype($data) !== 'string' ? VarStr::getMakeString($data) : $data)) > 0) {
                    $return = explode($delimiter, $data);
                } else {
                    $return = [];
                }
            }

        } else {
            $return = $data;
        }

        return $return;
    }


    /**
     * Преобразование значения переменной в тип = строчный!
     * Для последующих операций над переменной именно по сценарию строчного что-бы вернуть одно значение переменной.
     *
     * @note: Если передать второй параметр то это будет значением по умолчанию!
     *
     * @param string|null $delimiter
     * @return $this
     */
    public function toString($delimiter = null)
    {
        $default = count(func_get_args()) === 2 ? func_get_arg(1) : $this->getDefault();
        $this->data = static::getToString($this->data, $delimiter, $default);

        return $this;
    }


    /**
     * Преобразование значения переменной в тип = строчный!
     * Для последующих операций над переменной именно по сценарию строчного что-бы вернуть одно значение переменной.
     *
     * @param array|int   $data
     * @param string|null $delimiter
     * @param string      $default
     * @return string
     */
    static public function getToString($data, $delimiter = null, $default = '')
    {
        if (is_array($data)) {
            $return = $default;

            if (is_string($delimiter)) {
                $return = join($delimiter, $data);
            }

        } else {
            $return = $data;
        }

        return VarStr::getMakeString($return);
    }


    /**
     * Преобразование значения в строку
     *
     * @note: Если передать второй параметр то это будет значением по умолчанию!
     *
     * @param bool $recursive
     * @return $this
     */
    public function makeString($recursive = false)
    {
        $default = count(func_get_args()) === 2 ? func_get_arg(1) : $this->getDefault();
        $this->data = static::getMakeString($this->data, $default, $recursive);

        return $this;
    }


    /**
     * Преобразование значения в число с проверкой на минимальное значение
     *
     * @param mixed  $data
     * @param string $default
     * @param bool   $recursive
     * @return array|string
     */
    static public function getMakeString($data, $default = '', $recursive = false)
    {
        if (is_array($data) && is_array($return = [])) {

            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getMakeString($item, $default, $recursive);

                    } else {
                        $return[$key] = is_null($item) ? $default : VarStr::getMakeString($item);
                    }
                }
            }

        } else {
            $return = is_null($data) ? $default : VarStr::getMakeString($data);
        }

        return $return;
    }


    /**
     * Сокращённый метод работы со строкой с возвратом результата с вызовом метода get()
     *
     * @param string $option
     * @param bool   $recursive - флаг для обхода потомков
     * @param array  $remove    - символы для удаления из текста
     * @return array | string
     */
    public function getInput($option = 'small', $recursive = false, $remove = ["\t", "\n", "\r"])
    {
        return $this->input($option, $recursive, $remove)->get();
    }


    /**
     * Сокращённый метод работы со строкой
     *
     * @param string $option
     * @param bool   $recursive - флаг для обхода потомков
     * @param array  $remove    - символы для удаления из текста
     * @return $this
     */
    public function input($option = 'small', $recursive = false, $remove = ["\t", "\n", "\r"])
    {
        $this->__option = (string)$option;
        $this->makeString($recursive);

        if ($option === 'unchanged' || $option === 'raw') {
            return $this;
        }

        if ($option === "tags" && in_array(",", $remove)) {
            if (($index = array_search(",", $remove) !== false)) {
                unset($remove[$index]);
            }
        }

        switch ($option) {
            /**
             * Преобразование значений в строку формата price без копеек
             */
            case 'price':
                return $this->crop(14, $recursive)
                    ->makeFloat(0, "auto", true, $recursive)
                    ->numberFormat(0, '', '', $recursive);
                break;
            case 'price-upward':
                return $this->crop(14, $recursive)
                    ->makeFloat(0, "upward", true, $recursive)
                    ->numberFormat(0, '', '', $recursive);
                break;
            case 'price-downward':
                return $this->crop(14, $recursive)
                    ->makeFloat(0, "downward", true, $recursive)
                    ->numberFormat(0, '', '', $recursive);
                break;
            /**
             * Преобразование значений в строку формата price с указанием копеек
             */
            case 'cost':
                return $this->crop(14, $recursive)
                    ->makeFloat(2, "auto", true, $recursive)
                    ->numberFormat(2, '.', '', $recursive);
                break;
            case 'cost-upward':
                return $this->crop(14, $recursive)
                    ->makeFloat(2, "upward", true, $recursive)
                    ->numberFormat(2, '.', '', $recursive);
                break;
            case 'cost-downward':
                return $this->crop(14, $recursive)
                    ->makeFloat(2, "downward", true, $recursive)
                    ->numberFormat(2, '.', '', $recursive);
                break;
            /**
             * Over
             */
            case 'xs':
            case 'xs-html-encode':
            case 'sm':
            case 'sm-html-encode':
            case 'small':
            case 'small-html-encode':
            case 'text':
            case 'text-html-encode':
            case 'mediumtext':
            case 'mediumtext-html-encode':
            case 'longtext':
            case 'longtext-html-encode':
            case 'ids':
            case 'tags':
            case 'tags-html-encode':
                $this->trim(VariableOptions::TRIM_REMOVE_CHAR, $recursive);

                if (is_array($remove) && count($remove) > 0) {
                    $this->removeSymbol($remove, $recursive);
                }

                // кодирует символы в сущности
                if ($option === 'tags-html-encode' ||
                    $option === 'xs-html-encode' ||
                    $option === 'sm-html-encode' ||
                    $option === 'small-html-encode' ||
                    $option === 'text-html-encode' ||
                    $option === 'mediumtext-html-encode' ||
                    $option === 'longtext-html-encode') {
                    self::htmlEntityEncode(ENT_COMPAT | ENT_HTML5, 'UTF-8', false, $recursive);
                }

                break;

            case 'raw-html-decode':  // кодирует сущности в символы
                if ($option === 'raw-html-decode') {
                    self::htmlEntityDecode(ENT_COMPAT | ENT_HTML5, 'UTF-8', $recursive);
                }

                break;

            case 'raw-html-encode':  // кодирует символы в сущности
                if ($option === 'raw-html-encode') {
                    self::htmlEntityEncode(ENT_COMPAT | ENT_HTML5, 'UTF-8', false, $recursive);
                }

                break;
        }

        switch ($option) {
            case 'xs':
            case 'xs-html-decode':
                $this->crop(50, $recursive);

                if ($option === 'xs-html-decode') { // кодирует сущности в символы
                    self::htmlEntityDecode(ENT_COMPAT | ENT_HTML5, 'UTF-8', $recursive);
                }
                break;

            case 'sm':
            case 'sm-html-decode':
                $this->crop(100, $recursive);

                if ($option === 'sm-html-decode') { // кодирует сущности в символы
                    self::htmlEntityDecode(ENT_COMPAT | ENT_HTML5, 'UTF-8', $recursive);
                }
                break;

            case 'small':
            case 'small-html-decode':
                $this->crop(255, $recursive);

                if ($option === 'small-html-decode') { // кодирует сущности в символы
                    self::htmlEntityDecode(ENT_COMPAT | ENT_HTML5, 'UTF-8', $recursive);
                }
                break;

            case 'text':
            case 'text-html-decode':
                $this->crop(21500, $recursive);

                if ($option === 'text-html-decode') { // кодирует сущности в символы
                    self::htmlEntityDecode(ENT_COMPAT | ENT_HTML5, 'UTF-8', $recursive);
                }
                break;

            case 'tags':
            case 'tags-html-decode':
            case 'mediumtext':
            case 'mediumtext-html-decode':
                $this->crop(5592000, $recursive);

                if (in_array($option, ['mediumtext-html-decode' . 'tags-html-decode'])) { // кодирует сущности в символы
                    self::htmlEntityDecode(ENT_COMPAT | ENT_HTML5, 'UTF-8', $recursive);
                }
                break;

            case 'longtext':
            case 'longtext-html-decode':
                $this->crop(1431655000, $recursive);

                if ($option === 'longtext-html-decode') { // кодирует сущности в символы
                    self::htmlEntityDecode(ENT_COMPAT | ENT_HTML5, 'UTF-8', $recursive);
                }
                break;

            /**
             * Проверка в строке или массиве наличие в ней идентификаторов.
             * Идентификаторы проходят проверки на целое положительно число.
             *
             * 1,2,3,4,5 ...
             */
            case 'ids':
                if ($option === 'ids') {
                    $this->ids();
                }
                break;
        }

        if ($option === "tags") {
            $this->tags();
        }

        return $this;
    }


    /**
     * Устанавливает как преобразовывать пустую строку по окончанию обработки и вывода значения
     *
     * @param mixed $var
     * @return $this
     */
    public function convertEmptyString($var = null)
    {
        $this->convertAnEmptyString = $var;

        return $this;
    }


    /**
     * Сокращённый метод работы с числами с плавающей точкой и возвратом результата с вызовом метода get()
     *
     * @param string $option
     * @param string $round     [auto, upward, downward] - тип округления
     * @param bool   $positive  - флаг положительного числа
     * @param bool   $recursive - флаг для обхода потомков
     * @return float | array
     */
    public function getFloat($option = null, $round = "auto", $positive = false, $recursive = false)
    {
        return $this->float($option, $round, $positive, $recursive)->get();
    }

    /**
     * Сокращённый метод работы с числами с плавающей точкой
     *
     * @param string | integer $option    [$decimals] - если указали число считаем это определение точности
     * @param string           $round     [auto, upward, downward] - тип округления десятичного значения
     * @param bool             $positive  - флаг положительного числа
     * @param bool             $recursive - флаг для обхода потомков
     * @return $this
     */
    public function float($option = null, $round = "auto", $positive = false, $recursive = false)
    {
        if (is_string($option)) {
            $this->__option = $option;
        }

        switch ($option) {
            /**
             * Преобразование значений в денежную единицу без копеек
             */
            case 'price':
                return $this->makeFloat(0, $round, true, $recursive);
                break;

            /**
             * Преобразование значений в денежную единицу с указанием копеек
             */
            case 'cost':
                return $this->makeFloat(2, $round, true, $recursive);
                break;
        }

        // если указали число считаем это за определение точности
        $decimals = is_numeric($option) && $option >= 0 ? $option : 12;

        return $this->makeFloat($decimals, $round, $positive, $recursive);
    }


    /**
     * Преобразование значения в число с плавающей точкой
     *
     * @note: возможны отрицательные значения!
     *
     * @param int    $decimals  - точность
     * @param string $round     [auto, upward, downward] - тип округления десятичного значения
     * @param bool   $positive  - флаг положительного числа, >= 0
     * @param bool   $recursive - флаг для обхода потомков
     * @return $this
     */
    public function makeFloat($decimals = 2, $round = "auto", $positive = true, $recursive = false)
    {
        $this->data = static::getMakeFloat($this->data, $decimals, $round, $this->getDefault(), $positive, $recursive);

        return $this;
    }


    /**
     * Преобразование значения числа с плавающей точкой
     *
     * @note: возможны отрицательные значения!
     *
     * @param array|int $data
     * @param int       $decimals  - точность
     * @param string    $round     [auto, upward, downward] - тип округления десятичного значения
     * @param float     $default
     * @param bool      $positive  - флаг положительного числа, >= 0
     * @param bool      $recursive - флаг для обхода потомков
     * @return array|int
     */
    static public function getMakeFloat(
        $data,
        $decimals = 2,
        $round = "auto",
        $default = 0.0,
        $positive = true,
        $recursive = false
    ) {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {

                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getMakeFloat($item, $decimals, $round, $default, $positive, $recursive);

                    } else {

                        if ($positive) {
                            $return[$key] = VarFloat::getMakePositive($item, $decimals, $round, $default);
                        } else {
                            $return[$key] = VarFloat::getMake($item, $decimals, $round, $default);
                        }
                    }
                }
            }

        } else {

            if ($positive) {
                $return = VarFloat::getMakePositive($data, $decimals, $round, $default);

            } else {
                $return = VarFloat::getMake($data, $decimals, $round, $default);
            }
        }

        return $return;
    }


    /**
     * Сокращённый метод работы с цифрами и возвратом результата с вызовом метода get()
     *
     * @param string $option
     * @param bool   $positive
     * @param bool   $recursive - флаг для обхода потомков
     * @return integer | array
     */
    public function getInteger($option = null, $positive = false, $recursive = false)
    {
        return $this->integer($option, $positive, $recursive)->get();
    }


    /**
     * Сокращённый метод работы с цифрами
     *
     * @param string $option
     * @param bool   $positive
     * @param bool   $recursive - флаг для обхода потомков
     * @return $this
     */
    public function integer($option = null, $positive = false, $recursive = false)
    {
        if (is_string($option)) {
            $this->__option = $option;
        }

        // для работы логики filter принудительно указываем флаг положительного числа в FALSE
        if ($option === 'filter') {
            $positive = false;
        }

        // для переключателя делаем условие определения значения более мягкие и разрешаем on | yes | true ...
        $strict = in_array($option, ['toggle', 'ids']) ? false : true;

        $this->makeInteger($positive, $recursive, $strict);

        /**
         * Для проверки фильтров где допускаются значения от -1,0,1,2,3,4,5 ...
         * -1 не определено
         * 0 не выбрано
         * 1,2,3,4,5 ...
         */
        if ($option === 'filter') {
            $this->minInteger(-1, $recursive);
        }

        switch ($option) {
            /**
             * Для проверки списков где допускаются значения от 0 (не выбрано), 1, 2, 3, 4, 5 ...
             * Ещё подходит для определения ID где 0 допустима как запись не имеющая идентификатора ( новая )
             */
            case 'option':
            case 'id':
            case 'price':
                $this->minInteger(0, $recursive);
                break;

            case 'year':
                $this->makeInteger(true, $recursive)->minInteger(1970, $recursive);
                break;

            /**
             * Для пагинатора не допускаем значение страницы ниже один!
             */
            case 'page':
            case 'pagination':
                $this->makeInteger(true, $recursive)->minInteger(1, $recursive);
                break;

            /**
             * Для переключателей нужно всего 2 значения 0) off и 1) on
             * Не устанавливаем принудительное значение по умолчанию что-бы иметь возможность гибко настроить поведение!
             */
            case 'toggle':
                $this->minInteger(0, $recursive)->maxInteger(1, false, $recursive);
                break;

            /**
             * Проверка значений массива или текущего числа на наличие идентификатора(ов).
             * Идентификаторы проходят проверки на целое положительно число.
             *
             * @note Если проверяемая переменная содержала строку с идентификаторами,
             * она будет преобразована в число по правилам строгой типизации и только после будет произведена проверка на ID!
             * @note Для работы со списками из строк, работайте с методом input('ids');
             *
             * 1,2,3,4,5 ...
             */
            case 'ids':
                $this->ids(null, 'single', $recursive);
                break;
        }

        return $this;
    }


    /**
     * Преобразование значения в целые положительные числа, а пустые значения в null
     *
     * @param string $round     - [auto, upward, downward] - тип округления десятичного значения
     * @param int    $decimals  - точность
     * @param bool   $recursive - флаг для обхода потомков
     * @return array | integer | null
     */
    public function getPrice($round = "auto", $decimals = 0, $recursive = false)
    {
        $data = $this->input('sm')->pregReplace("/[^0-9\.\,]/", "")->get();

        return static::getMakePrice($data, $this->getDefault(), $recursive, $decimals, $round);
    }


    /**
     * Заменяет переданные пустые значения на null или преобразовывает в integer
     *
     * @param string | array $data      - значения уже заранее очищены от всего кроме цифр
     * @param null           $default   - значение по умолчанию
     * @param bool           $recursive - флаг для обхода потомков
     * @param int            $decimals  - точность
     * @param string         $round     - [auto, upward, downward] - тип округления десятичного значения
     * @return array | integer | null
     */
    static public function getMakePrice($data, $default = null, $recursive = false, $decimals = 0, $round = "auto")
    {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getMakePrice($item, $default, $recursive, $decimals, $round);

                    } else {
                        $item = is_string($item) ? $item : static::getMakeString($item);
                        $return[$key] = static::getMakePrice($item, $default, false, $decimals, $round);
                    }
                }
            }

        } else {
            $data = is_string($data) ? $data : static::getMakeString($data);

            if (! empty(str_replace([".", ","], ["", ""], $data))) {
                $data = VarFloat::getMakePositive($data, $decimals, $round);
                $return = (int)static::getNumberFormat($data, $decimals, '', '', false);

            } else {
                $return = $default;
            }
        }

        return $return;
    }


    /**
     * Преобразование значения в число с плавающей запятой, а пустые значения в float
     *
     * @param string  $round     - [auto, upward, downward] - тип округления десятичного значения
     * @param int     $decimals  - точность
     * @param string  $separator - разделитель точности
     * @param boolean $recursive - флаг для обхода потомков
     * @return array | float | null
     */
    public function getCost($round = "auto", $decimals = 2, $separator = '.', $recursive = false)
    {
        $data = $this->input('sm')->pregReplace("/[^0-9\.\,]/", "")->get();

        return static::getMakeCost($data, $this->getDefault(), $recursive, $decimals, $round, $separator);
    }


    /**
     * Заменяет переданные пустые значения на null или преобразовывает в float
     *
     * @param string | array $data      - значения уже заранее очищены от всего кроме цифр, запятой и точки
     * @param null           $default   - значение по умолчанию
     * @param boolean        $recursive - флаг для обхода потомков
     * @param int            $decimals  - точность
     * @param string         $round     - [auto, upward, downward] - тип округления десятичного значения
     * @param string         $separator - разделитель точности
     * @return array | float | null
     */
    static public function getMakeCost(
        $data,
        $default = null,
        $recursive = false,
        $decimals = 2,
        $round = "auto",
        $separator = '.'
    ) {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getMakeCost($item, $default, $recursive, $decimals, $round, $separator);

                    } else {
                        $item = is_string($item) ? $item : static::getMakeString($item);
                        $return[$key] = static::getMakeCost($item, $default, false, $decimals, $round, $separator);
                    }
                }
            }

        } else {
            $data = is_string($data) ? $data : static::getMakeString($data);

            if (! empty(str_replace([".", ","], ["", ""], $data))) {
                $data = VarFloat::getMakePositive($data, $decimals, $round);
                $return = (float)static::getNumberFormat($data, $decimals, $separator, '', false);

            } else {
                $return = $default;
            }
        }

        return $return;
    }


    /**
     * Преобразование значения в целое число
     *
     * @note: возможны отрицательные значения!
     *
     * @param bool $positive  - флаг положительного числа, >= 0
     * @param bool $recursive - флаг для обхода потомков
     * @param bool $strict    - флаг для преобразования дополнительных значений типа "on|off|no|yes" в число
     * @return $this
     */
    public function makeInteger($positive = true, $recursive = false, $strict = true)
    {
        $this->data = static::getMakeInteger($this->data, $this->getDefault(), $positive, $recursive, $strict);

        return $this;
    }


    /**
     * Преобразование значения в целое число
     *
     * @note: возможны отрицательные значения!
     *
     * @param array|int $data
     * @param int       $default   - значение по умолчанию
     * @param bool      $positive  - флаг положительного числа, >= 0
     * @param bool      $recursive - флаг для обхода потомков
     * @param bool      $strict    - флаг для преобразования дополнительных значений типа "on|off|no|yes" в число
     * @return array|int
     */
    static public function getMakeInteger($data, $default = 0, $positive = true, $recursive = false, $strict = true)
    {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {

                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getMakeInteger($item, $default, $positive, $recursive);

                    } else {
                        if ($positive) {
                            $return[$key] = VarInt::getMakePositiveInteger($item, $default, $strict);
                        } else {
                            $return[$key] = VarInt::getMakeInteger($item, $default, $strict);
                        }
                    }
                }
            }

        } else {
            $data = $data === "" ? null : $data;

            if ($positive) {
                $return = VarInt::getMakePositiveInteger($data, $default, $strict);
            } else {
                $return = VarInt::getMakeInteger($data, $default, $strict);
            }
        }

        return $return;
    }


    /**
     * Преобразование значения в целое число.
     *
     * @note этот метод проверяет на минимальное значение из указанного числа по умолчанию!
     * @note : возможны отрицательные значения!
     *
     * @param bool $recursive - флаг для обхода потомков
     * @return array | integer
     */
    public function getIntegerNotLess($recursive = false)
    {
        return $this->integerNotLess($recursive)->get();
    }


    /**
     * Преобразование значения в целое число.
     *
     * @note этот метод проверяет на минимальное значение из указанного числа по умолчанию!
     * @note : возможны отрицательные значения!
     *
     * @param bool $recursive - флаг для обхода потомков
     * @return $this
     */
    public function integerNotLess($recursive = false)
    {
        $this->data = static::getMinInteger($this->data, $this->getDefault(), $recursive);

        return $this;
    }


    /**
     * Преобразование значения в целое число с проверкой его на минимальное значение
     * Более читабельный вариант если устанавливать правило минимального значение с отличным от default значения!
     *
     * @note: возможны отрицательные значения!
     *
     * @param int  $default   - значение по умолчанию
     * @param bool $recursive - флаг для обхода потомков
     * @return $this
     */
    public function minInteger($default = 0, $recursive = false)
    {
        $this->data = static::getMinInteger($this->data, $default, $recursive);

        return $this;
    }


    /**
     * Преобразование значения в целое число с проверкой его на минимальное значение
     *
     * @note: возможны отрицательные значения!
     *
     * @param mixed $data
     * @param int   $default   - значение по умолчанию
     * @param bool  $recursive - флаг для обхода потомков
     * @return array|int
     */
    static public function getMinInteger($data, $default = 0, $recursive = false)
    {
        $default = VarInt::getMakeInteger($default);

        if (is_array($data) && is_array($return = [])) {

            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getMinInteger($item, $default, $recursive);

                    } else {
                        $item = VarInt::getMakeInteger($item, $default);
                        $return[$key] = $item >= $default ? $item : $default;
                    }
                }
            }

        } else {
            $data = $data === "" ? null : $data;
            $data = VarInt::getMakeInteger($data, $default);
            $return = $data >= $default ? $data : $default;
        }

        return $return;
    }


    /**
     * Преобразование значения в целое число с проверкой его на максимальное значение
     * Более читабельный вариант если устанавливать правило минимального значение с отличным от default значения!
     *
     * @note: возможны отрицательные значения!
     *
     * @param int  $max
     * @param bool $toDefault - флаг преобразования числа вышедего за пределы
     * @param bool $recursive - флаг для обхода потомков
     * @return $this
     */
    public function maxInteger($max = 0, $toDefault = true, $recursive = false)
    {
        $this->data = static::getMaxInteger($this->data, $max, $toDefault, $this->getDefault(), $recursive);

        return $this;
    }


    /**
     * Преобразование значения в целое число с проверкой его на максимальное значение
     *
     * @note: возможны отрицательные значения!
     *
     * @param mixed $data
     * @param int   $max       - число предела
     * @param bool  $toDefault - флаг преобразования числа вышедшего за пределы в default или max
     * @param int   $default   - значение по умолчанию
     * @param bool  $recursive - флаг для обхода потомков
     * @return array|int
     */
    static public function getMaxInteger($data, $max = 0, $toDefault = true, $default = 0, $recursive = false)
    {
        $default = VarInt::getMakeInteger($default);

        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getMaxInteger($item, $max, $toDefault, $default, $recursive);

                    } else {
                        $item = VarInt::getMakeInteger($item, $default);
                        $return[$key] = $item;

                        if ($item > $max) {
                            $return[$key] = $toDefault ? $default : $max;
                        }
                    }
                }
            }
        } else {
            $data = $data === "" ? null : $data;
            $data = VarInt::getMakeInteger($data, $default);
            $return = $data;

            if ($data > $max) {
                $return = $toDefault ? $default : $max;
            }
        }

        return $return;
    }


    /**
     * Форматирует число с разделением групп
     *
     * @note результат работы будет в виде строки и нужно не забывать после привести к нужному типу!
     *
     * @param int    $decimals      - точность
     * @param string $separator     - разделитель точности
     * @param string $thousands_sep - разделитель тысяч
     * @param bool   $recursive     - флаг для обхода потомков
     * @return $this
     */
    public function numberFormat(
        $decimals = 2,
        $separator = '.',
        $thousands_sep = '',
        $recursive = false
    ) {
        $this->data = static::getNumberFormat($this->data, $decimals, $separator, $thousands_sep, $this->getDefault(), $recursive);

        return $this;
    }


    /**
     * Форматирует число с разделением групп
     *
     * @note результат работы будет в виде строки и нужно не забывать после привести к нужному типу!
     *
     * @param mixed  $data
     * @param int    $decimals      - точность
     * @param string $separator     - разделитель точности
     * @param string $thousands_sep - разделитель тысяч
     * @param int    $default       - значение по умолчанию
     * @param bool   $recursive     - флаг для обхода потомков
     * @return array|string
     */
    static public function getNumberFormat(
        $data,
        $decimals = 2,
        $separator = '.',
        $thousands_sep = '',
        $default = 0,
        $recursive = false
    ) {
        $decimals = $decimals >= 0 ? $decimals : 2;

        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);


                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getNumberFormat($item, $recursive);

                    } else {
                        $item = is_string($item) ? $item : strval($item);

                        // Помощь при опечатках если разделители классические
                        if ($separator == '.') {
                            $item = VarStr::replaceOnce(",", ".", $item);
                        } elseif ($separator == ',') {
                            $item = VarStr::replaceOnce(".", ",", $item);
                        }

                        try {
                            $return[$key] = number_format($item, $decimals, $separator, $thousands_sep);
                        } catch (\Throwable $e) {
                            $return[$key] = number_format($default, $decimals, $separator, $thousands_sep);
                        }
                    }
                }
            }
        } else {
            $data = is_string($data) ? $data : strval($data);

            // Помощь при опечатках если разделители классические
            if ($separator == '.') {
                $data = VarStr::replaceOnce(",", ".", $data);
            } elseif ($separator == ',') {
                $data = VarStr::replaceOnce(".", ",", $data);
            }

            try {
                $return = number_format($data, $decimals, $separator, $thousands_sep);
            } catch (\Throwable $e) {
                $return = number_format($default, $decimals, $separator, $thousands_sep);
            }
        }

        return $return;
    }


    /**
     * Преобразование значений в MD5-хэш
     *
     * @param bool $recursive - флаг для обхода потомков
     * @return $this
     */
    public function makeMd5($recursive = false)
    {
        $this->data = static::getMd5($this->data, $recursive);

        return $this;
    }


    /**
     * Преобразование значений в MD5-хэш
     *
     * @param mixed $data
     * @param bool  $recursive - флаг для обхода потомков
     * @return array|string
     */
    static public function getMd5($data, $recursive = false)
    {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getMd5($item, $recursive);

                    } else {
                        $return[$key] = md5($item);
                    }
                }
            }
        } else {
            $return = md5(VarStr::getMakeString($data));
        }

        return $return;
    }


    /**
     * Удаляет HTML и PHP-теги из данных
     *
     * @param string $allowable_tags
     * @param bool   $recursive - флаг для обхода потомков
     * @return $this
     */
    public function stripTags($allowable_tags = '', $recursive = false)
    {
        $this->data = static::getStripTags($this->data, $allowable_tags, $recursive);

        return $this;
    }


    /**
     * Удаляет HTML и PHP-теги из данных
     *
     * @param string $data
     * @param string $allowable_tags
     * @param bool   $recursive - флаг для обхода потомков
     * @return array|string
     */
    static public function getStripTags($data = '', $allowable_tags = '', $recursive = false)
    {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getStripTags($item, $allowable_tags, $recursive);

                    } else {
                        $return[$key] = strip_tags($item, $allowable_tags);
                    }
                }
            }
        } else {
            $return = strip_tags($data, $allowable_tags);
        }

        return $return;
    }


    /**
     * Вставляет HTML-код разрыва строки перед каждым переводом строки
     *
     * @param bool $is_xhtml
     * @param bool $recursive - флаг для обхода потомков
     * @return $this
     */
    public function nl2br($is_xhtml = false, $recursive = false)
    {
        $this->data = static::getNl2br($this->data, $is_xhtml, $recursive);

        return $this;
    }


    /**
     * Вставляет HTML-код разрыва строки перед каждым переводом строки
     *
     * @param string $data
     * @param bool   $is_xhtml
     * @param bool   $recursive - флаг для обхода потомков
     * @return array|string
     */
    static public function getNl2br($data = '', $is_xhtml = false, $recursive = false)
    {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getNl2br($item, $is_xhtml, $recursive);

                    } else {
                        $return[$key] = nl2br($item, $is_xhtml);
                    }
                }
            }

        } else {
            $return = nl2br($data, $is_xhtml);
        }

        return $return;
    }


    /**
     * Заменяет все вхождения строки поиска на строку замены
     *
     * @param string|array $search    - искомое значение
     * @param string|array $replace   - значение замены
     * @param bool         $recursive - флаг для обхода потомков
     * @return $this
     */
    public function strReplace($search = [], $replace = [], $recursive = false)
    {
        $this->data = static::getStrReplace($this->data, $search, $replace, $recursive);

        return $this;
    }


    /**
     * Заменяет все вхождения строки поиска на строку замены
     *
     * @param string|array $data      - строка или массив, в котором производится поиск и замена
     * @param string|array $search    - искомое значение
     * @param string|array $replace   - значение замены
     * @param bool         $recursive - флаг для обхода потомков
     * @return array|string
     */
    static public function getStrReplace($data = '', $search = [], $replace = [], $recursive = false)
    {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getStrReplace($item, $search, $replace, $recursive);

                    } else {
                        $return[$key] = str_replace($search, $replace, $item);
                    }
                }
            }

        } else {
            $return = str_replace($search, $replace, $data);
        }

        return $return;
    }


    /**
     * Преобразовывает строку(и) в нижний регистр (lower-case).
     *
     * @param bool $recursive - флаг для обхода потомков
     * @return $this
     */
    public function lower($recursive = false)
    {
        $this->data = static::getLower($this->data, $recursive);

        return $this;
    }


    /**
     * Преобразовывает строку(и) в нижний регистр (lower-case).
     *
     * @param string $data
     * @param bool   $recursive - флаг для обхода потомков
     * @return array|string
     */
    static public function getLower($data = '', $recursive = false)
    {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getLower($item, $recursive);

                    } else {
                        $return[$key] = VarStr::getLower($item);
                    }
                }
            }

        } else {
            $return = VarStr::getLower($data);
        }

        return $return;
    }


    /**
     * Преобразовывает строку(и) в верхний регистр (upper-case).
     *
     * @param bool $recursive - флаг для обхода потомков
     * @return $this
     */
    public function upper($recursive = false)
    {
        $this->data = static::getUpper($this->data, $recursive);

        return $this;
    }


    /**
     * Преобразовывает строку(и) в верхний регистр (upper-case).
     *
     * @param string $data
     * @param bool   $recursive - флаг для обхода потомков
     * @return array|string
     */
    static public function getUpper($data = '', $recursive = false)
    {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getUpper($item, $recursive);

                    } else {
                        $return[$key] = VarStr::getUpper($item);
                    }
                }
            }

        } else {
            $return = VarStr::getUpper($data);
        }

        return $return;
    }


    /**
     * Удаляет экранирование символов
     *
     * @param string|array $pattern     - Искомый шаблон. Может быть как строкой, так и массивом строк.
     * @param string|array $replacement - Строка или массив строк для замены.
     * @param bool         $recursive   - флаг для обхода потомков
     * @param int          $limit       - Максимально возможное количество замен каждого шаблона для каждой строки subject. По умолчанию равно -1 (без ограничений).
     * @return $this
     */
    public function pregReplace($pattern = '', $replacement = '', $recursive = false, $limit = -1)
    {
        $this->data = static::getPregReplace($this->data, $pattern, $replacement, $recursive, $limit);

        return $this;
    }


    /**
     * Удаляет экранирование символов ( статически метод для использования в контексте класса )
     *
     * @param string|array $data        -  Строка или массив строк для поиска и замены.
     * @param string|array $pattern     - Искомый шаблон. Может быть как строкой, так и массивом строк.
     * @param string|array $replacement - Строка или массив строк для замены.
     * @param bool         $recursive   - флаг для обхода потомков
     * @param int          $limit       - Максимально возможное количество замен каждого шаблона для каждой строки subject. По умолчанию равно -1 (без ограничений).
     * @return array|string
     */
    static public function getPregReplace($data, $pattern = '', $replacement = '', $recursive = false, $limit = -1)
    {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getPregReplace($item, $pattern, $replacement, $recursive, $limit);

                    } else {
                        $return[$key] = preg_replace($pattern, $replacement, (string)$item, $limit);
                    }
                }
            }

        } else {
            $return = preg_replace($pattern, $replacement, (string)$data, $limit);
        }

        return $return;
    }


    /**
     * Удаляет экранирование символов
     *
     * @param bool $recursive - флаг для обхода потомков
     * @return $this
     */
    public function stripSlashes($recursive = false)
    {
        $this->data = static::getStripSlashes($this->data, $recursive);

        return $this;
    }


    /**
     * Удаляет экранирование символов
     *
     * @param string|array $data
     * @param bool         $recursive - флаг для обхода потомков
     * @return array|string
     */
    static public function getStripSlashes($data, $recursive = false)
    {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getStripSlashes($item, $recursive);

                    } else {
                        $return[$key] = stripslashes($item);
                    }
                }
            }

        } else {
            $return = stripslashes((string)$data);
        }

        return $return;
    }


    /**
     * Экранирует строку с помощью слешей
     *
     * @note Экранируются следующие символы: одинарная кавычка ('), двойная кавычка ("), обратный слеш (\), NUL (байт NULL)
     *
     * @param bool $recursive - флаг для обхода потомков
     * @return $this
     */
    public function addSlashes($recursive = false)
    {
        $this->data = static::getAddSlashes($this->data, $recursive);

        return $this;
    }


    /**
     * Экранирует строку с помощью слешей
     *
     * @note Экранируются следующие символы: одинарная кавычка ('), двойная кавычка ("), обратный слеш (\), NUL (байт NULL)
     *
     * @param string|array $data
     * @param bool         $recursive - флаг для обхода потомков
     * @return array|string
     */
    static public function getAddSlashes($data, $recursive = false)
    {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getAddSlashes($item, $recursive);

                    } else {
                        $return[$key] = addslashes($item);
                    }
                }
            }
        } else {
            $return = addslashes((string)$data);
        }

        return $return;
    }


    /**
     * Кодирует HTML-сущности в специальные символы
     *
     * @example &amp;copy; > &copy; или &amp; > &
     *
     * @param int    $flags     - битовая маска из флагов определяющая режим обработки
     * @param string $encoding  - кодировка
     * @param bool   $recursive - флаг для обхода потомков
     * @return $this
     */
    public function htmlEntityDecode($flags = ENT_COMPAT | ENT_HTML5, $encoding = 'UTF-8', $recursive = false)
    {
        $this->data = static::getHtmlEntityDecode($this->data, $flags, $encoding, $recursive);

        return $this;
    }


    /**
     * Кодирует HTML-сущности в специальные символы
     *
     * @example: &amp;copy; > &copy; | &amp; > & | &quot; > " | &bull; > •
     *
     * @param        $data
     * @param int    $flags     - битовая маска из флагов определяющая режим обработки
     * @param string $encoding  - кодировка
     * @param bool   $recursive - флаг для обхода потомков
     * @return array|string
     */
    static public function getHtmlEntityDecode(
        $data,
        $flags = ENT_COMPAT | ENT_HTML5,
        $encoding = 'UTF-8',
        $recursive = false
    ) {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getHtmlEntityDecode($item, $flags, $encoding, $recursive);

                    } else {
                        $return[$key] = html_entity_decode($item, $flags, $encoding);
                    }
                }
            }
        } else {
            $return = html_entity_decode((string)$data, $flags, $encoding);
        }

        return $return;
    }


    /**
     * Кодирует только специальные символы в их HTML-сущности
     *
     * @note    Кодирует только символы &, ", ', <, >, для кодирования всех символов используйте self::htmlEntityEncode()
     * @example & > &amp; | " > &quot; | ' > &apos; | > в &lt; | < в &gt;
     *
     * @param int    $flags        - битовая маска из флагов определяющая режим обработки
     * @param string $encoding     - кодировка
     * @param bool   $doubleEncode - при выключении не будет преобразовывать существующие HTML-сущности. При включении приведет к преобразованию &apos; > &amp;&apos;
     * @param bool   $recursive    - флаг для обхода потомков
     * @return $this
     */
    public function htmlSpecialCharsEncode(
        $flags = ENT_COMPAT | ENT_HTML5,
        $encoding = 'UTF-8',
        $doubleEncode = true,
        $recursive = false
    ) {
        $this->data = static::getHtmlSpecialCharsEncode($this->data, $flags, $encoding, $doubleEncode, $recursive);

        return $this;
    }


    /**
     * Кодирует только специальные символы в их HTML-сущности
     *
     * @note    Кодирует только символы &, ", ', <, >, для кодирования всех символов используйте self::htmlEntityEncode()
     * @example & > &amp; | " > &quot; | ' > &apos; | > в &lt; | < в &gt;
     *
     * @param string|array $data
     * @param int          $flags        - битовая маска из флагов определяющая режим обработки
     * @param string       $encoding     - кодировка
     * @param bool         $doubleEncode - при выключении не будет преобразовывать существующие HTML-сущности. При включении приведет к преобразованию &apos; > &amp;&apos;
     * @param bool         $recursive    - флаг для обхода потомков
     * @return array|string
     */
    static public function getHtmlSpecialCharsEncode(
        $data,
        $flags = ENT_COMPAT | ENT_HTML5,
        $encoding = 'UTF-8',
        $doubleEncode = false,
        $recursive = false
    ) {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getHtmlEntityEncode($item, $flags, $encoding, $doubleEncode, $recursive);

                    } else {
                        $return[$key] = htmlspecialchars((string)$item, $flags, $encoding, $doubleEncode);
                    }
                }
            }
        } else {
            $return = htmlspecialchars((string)$data, $flags, $encoding, $doubleEncode);
        }

        return $return;
    }


    /**
     * Кодирует ( все допустимые! ) символы в соответствующие HTML-сущности
     * Если надо преобразовать &copy; > &amp;copy; следует четвертый параметр $htmlEncode установить в TRUE
     *
     * @note    для преобразования только символов &, ", ', <, > используйте self::htmlSpecialCharsEncode() !
     * @example & > &amp; | " > &quot;
     *
     * @param int    $flags        - битовая маска из флагов определяющая режим обработки
     * @param string $encoding     - кодировка
     * @param bool   $doubleEncode - при выключении не будет преобразовывать существующие HTML-сущности. При включении приведет к преобразованию &copy; > &amp;copy;
     * @param bool   $recursive    - флаг для обхода потомков
     * @return $this
     */
    public function htmlEntityEncode(
        $flags = ENT_COMPAT | ENT_HTML5,
        $encoding = 'UTF-8',
        $doubleEncode = false,
        $recursive = false
    ) {
        $this->data = static::getHtmlEntityEncode($this->data, $flags, $encoding, $doubleEncode, $recursive);

        return $this;
    }


    /**
     * Кодирует ( все допустимые! ) символы в соответствующие HTML-сущности
     * Если надо преобразовать &copy; > &amp;copy; следует четвертый параметр $htmlEncode установить в TRUE
     *
     * @note    для преобразования только символов &, ", ', <, > используйте self::htmlSpecialCharsEncode() !
     * @example & > &amp; | " > &quot;
     *
     * @param string|array $data
     * @param int          $flags        - битовая маска из флагов определяющая режим обработки
     * @param string       $encoding     - кодировка
     * @param bool         $doubleEncode - при выключении не будет преобразовывать существующие HTML-сущности. При включении приведет к преобразованию &copy; > &amp;copy;
     * @param bool         $recursive    - флаг для обхода потомков
     * @return array|string
     */
    static public function getHtmlEntityEncode(
        $data,
        $flags = ENT_COMPAT | ENT_HTML5,
        $encoding = 'UTF-8',
        $doubleEncode = false,
        $recursive = false
    ) {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getHtmlEntityEncode($item, $flags, $encoding, $doubleEncode, $recursive);

                    } else {
                        $return[$key] = htmlentities((string)$item, $flags, $encoding, $doubleEncode);
                    }
                }
            }
        } else {
            $return = htmlentities((string)$data, $flags, $encoding, $doubleEncode);
        }

        return $return;
    }


    /**
     * Перевод данных в дату с проверкой по формату
     *
     * @param string $format
     * @param bool   $recursive - флаг для обхода потомков
     * @return $this
     */
    public function makeDate($format = 'Y-m-d', $recursive = false)
    {
        $this->data = static::getMakeDate($this->data, $this->getDefault(), $format, $recursive);

        return $this;
    }


    /**
     * Проверка даты по указанному формату
     *
     * @param mixed  $data
     * @param mixed  $default
     * @param string $format
     * @param bool   $recursive - флаг для обхода потомков
     * @return array|null|string
     */
    static public function getMakeDate($data, $default = null, $format = 'Y-m-d', $recursive = false)
    {
        if (is_array($data) && is_array($return = [])) {

            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getMakeDate($item, $default, $format, $recursive);

                    } else {
                        $return[$key] = ! is_string($item) ? $default : VarStr::makeDate($item, $default, $format);
                    }
                }
            }

        } else {
            $return = ! is_string($data) ? $default : VarStr::makeDate($data, $default, $format);
        }

        return $return;
    }


    /**
     * Удаляет пробелы из начала и конца строки (или другие символы при передачи их вторым параметром )
     *
     * @param null|array|string $removeChar
     * @param bool              $recursive - флаг для обхода потомков
     * @return $this
     */
    public function trim($removeChar = " \t\n\r\0\x0B", $recursive = false)
    {
        $this->data = static::getTrim($this->data, $removeChar, $recursive);

        return $this;
    }


    /**
     * Удаляет пробелы из начала и конца строки (или другие символы при передачи их вторым параметром )
     *
     * @param string|array      $data
     * @param null|array|string $removeChar - список символов для удаления
     * @param bool              $recursive  - флаг для обхода потомков
     * @return array|string
     */
    static public function getTrim($data = '', $removeChar = " \t\n\r\0\x0B", $recursive = false)
    {
        if (is_array($data) && is_array($default = [])) {

            if (count($data) > 0) {
                $default = VarArray::trim($data, $removeChar, $recursive);
            }

        } else {
            $default = VarStr::trim($data, $removeChar);
        }

        return $default;
    }


    /**
     * Удаление указанных символов из значений
     *
     * @param null|array|string $removeChar
     * @param bool              $recursive - флаг для обхода потомков
     * @return $this
     */
    public function removeSymbol($removeChar = ["\n", "\r", "\t"], $recursive = false)
    {
        $this->data = static::getRemoveSymbol($this->data, $removeChar, $recursive);

        return $this;
    }


    /**
     * Удаление указанных символов из значений
     *
     * @param string|array      $data
     * @param null|array|string $removeChar
     * @param bool              $recursive - флаг для обхода потомков
     * @return string
     */
    static public function getRemoveSymbol($data = '', $removeChar = ["\n", "\r", "\t"], $recursive = false)
    {
        $removeChar = is_array($removeChar) ? $removeChar : [VarStr::getMakeString($removeChar)];

        if (is_array($data) && is_array($default = [])) {

            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $default[$key] = static::getRemoveSymbol($item, $removeChar, $recursive);

                    } else {
                        $default[$key] = VarStr::getRemoveSymbol($item, $removeChar);
                    }
                }
            }

        } else {
            $default = VarStr::getRemoveSymbol($data, $removeChar);
        }

        return $default;
    }


    /**
     * Удаление значений из массива
     *
     * @param array $remove    ['', 0, null, 'null']
     * @param bool  $recursive - флаг для обхода потомков
     * @return $this
     */
    public function removeItems($remove = ['', 0, null, 'null'], $recursive = false)
    {
        $this->data = static::getRemoveItems($this->data, $remove, $recursive);

        return $this;
    }


    /**
     * Удаление значений из массива ( для строк поведение не предусмотрено! )
     *
     * @param string $data
     * @param array  $remove    ['', 0, null, 'null']
     * @param bool   $recursive - флаг для обхода потомков
     * @return array
     */
    static public function getRemoveItems($data = '', $remove = [0, '', null, 'null'], $recursive = false)
    {
        if (is_array($data) && count($data) > 0) {
            reset($data);

            if ($recursive === true) {
                foreach ($data as $key => $item) {
                    if (is_array($item)) {
                        $data[$key] = static::getRemoveItems($item, $remove, $recursive);
                    } else {
                        if (in_array($item, $remove)) {
                            $data[$key] = null;
                            unset($data[$key]);
                        }
                    }
                }

            } else {
                $data = VarArray::getRemove($data, $remove);
            }
        }

        return $data;
    }


    /**
     * Обрезает строку до 250 символов ( без всяких условий )
     *
     * @param null|int $length
     * @param bool     $recursive - флаг для обхода потомков
     * @return $this
     */
    public function crop($length = 250, $recursive = false)
    {
        $this->data = static::getCrop($this->data, $length, $recursive);

        return $this;
    }


    /**
     * Обрезает строку до указанных символов
     *
     * @param string|array $data
     * @param null|int     $length
     * @param bool         $recursive - флаг для обхода потомков
     * @return array|string
     */
    static public function getCrop($data = '', $length = 250, $recursive = false)
    {
        if (is_null($length)) {
            return $data;
        }

        if (is_array($data) && is_array($default = [])) {

            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $default[$key] = static::getCrop($item, $length, $recursive);

                    } else {
                        $default[$key] = VarStr::crop($item, $length);
                    }
                }
            }

        } else {
            $default = VarStr::crop(VarStr::getMakeString($data), $length);
        }

        return $default;
    }


    /**
     * Сокращает текст по параметрам
     *
     * @param null|int  $length
     * @param string    $end
     * @param bool|true $transform - преобразование символов
     * @param bool|true $smart     - флаг включающий умную систему резки с учётом целостности слов
     * @param bool      $recursive - флаг для обхода потомков
     * @return $this
     */
    public function reduce($length = 250, $end = '', $transform = true, $smart = true, $recursive = false)
    {
        $this->data = static::getReduce($this->data, $length, $end, $transform, $smart, $recursive);

        return $this;
    }


    /**
     * Сокращает текст по параметрам
     *
     * @param string|array $data
     * @param null|int     $length
     * @param string       $end
     * @param bool|true    $transform - преобразование символов
     * @param bool|true    $smart     - флаг включающий умную систему резки с учётом целостности слов
     * @param bool         $recursive - флаг для обхода потомков
     * @return array|string
     */
    static public function getReduce(
        $data = '',
        $length = 250,
        $end = '',
        $transform = true,
        $smart = true,
        $recursive = false
    ) {
        if (is_null($length)) {
            return $data;
        }

        $default = VarStr::getMakeString($data);

        if (is_string($data) && mb_strlen($data) > 0) {
            return VarStr::reduce($data, $length, $end, $transform, $smart);
        }

        if (is_array($data) && is_array($default = [])) {

            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $default[$key] = static::getReduce($item, $length, $end, $transform, $smart, $recursive);

                    } else {
                        $default[$key] = VarStr::reduce($item, $length, $end, $transform, $smart);
                    }
                }
            }
        }

        return $default;
    }


    /**
     * Альтернатива in_array но только тут ещё значения default
     *
     * @param array $needle
     * @param bool  $strict
     * @param bool  $recursive - флаг для обхода потомков
     * @return $this
     */
    public function inArray(array $needle = [], $strict = false, $recursive = false)
    {
        $this->data = static::getInArray($this->data, $this->getDefault(), $needle, $strict, $recursive);

        return $this;
    }


    /**
     * Альтернатива in_array но только тут ещё значения default
     *
     * @param array $data
     * @param       $default
     * @param array $needle
     * @param bool  $strict
     * @param bool  $recursive - флаг для обхода потомков
     * @return bool
     */
    static public function getInArray($data, $default, $needle = [], $strict = false, $recursive = false)
    {
        if (is_array($data) && is_array($return = [])) {

            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getInArray($item, $default, $needle, $strict, $recursive);

                    } else {
                        $item = static::getTrim(VarStr::getMakeString($item));
                        $return[$key] = in_array($item, $needle, $strict) ? $item : $default;
                    }
                }
            }

        } else {
            $data = static::getTrim(VarStr::getMakeString($data));
            $return = in_array($data, $needle, $strict) ? $data : $default;
        }

        return $return;
    }


    /**
     * Проверка строки или массива строк в которых содержатся списки идентификаторов.
     *
     * @param string|null $delimiter - разделитель строки
     * @param string      $unique    - флаг для проверки значений на уникальное повторение
     * @param bool        $recursive - флаг для обхода потомков
     * @return $this
     */
    public function ids($delimiter = ',', $unique = 'single', $recursive = false)
    {
        $this->data = static::getIds($this->data, $this->getDefault(), $delimiter, $unique, $recursive);

        return $this;
    }


    /**
     * Проверка строки или массива строк в которых содержатся списки идентификаторов.
     *
     * @note ID это число больше нуля. Если условие наличия числа в проверках не подтверждается и значения $default не равны числу больше нуля,
     * данные будут сокращены или совсем не созданы!
     * @note Если данные являются массивом и надо только проверить их значения, следует указать значение разделителя как NULL
     *
     * @param array|string $data
     * @param mixed        $default   - значение по умолчанию
     * @param string|null  $delimiter - разделитель строки
     * @param string       $unique    - флаг проверки уникального значения. Если указали `single`, будет проверка в текущем ряду
     * @param bool         $recursive - флаг для обхода потомков
     * @param array        $tmp
     * @return array|string
     */
    static function getIds($data, $default = null, $delimiter = ',', $unique = 'single', $recursive = false, &$tmp = [])
    {
        if (is_array($data) && is_array($return = [])) {

            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $return[$key] = static::getIds($item, $default, $delimiter, $unique, $recursive, $tmp);

                    } else {
                        $items = static::getTrim($item);

                        // логика разбития строки массива по разделителю
                        if (! is_null($delimiter) && $items != '') {
                            $items = VarStr::explode($delimiter, $items, []);
                            $items = VarArray::getRemove(static::getMinInteger($items), 0);

                            // Проверка на уникальность в текущем списке
                            $items = $unique === 'single' ? array_unique($items) : $items;

                            // Если указали проверять все значения на уникальность, проверяем текущий список
                            if ($unique == 'all') {
                                foreach ($items as $k => $i) {
                                    if (in_array($i, $tmp)) {
                                        unset($items[$k]);
                                    }
                                }
                            }

                            // Если указали проверять все значения на уникальность, записываем текущие значения в список всех данных
                            if ($unique == 'all' && count($items)) {
                                $tmp = array_merge($tmp, $items);
                                $tmp = array_unique($tmp);
                            }

                            if (count($items) > 0) {
                                $return[$key] = join($delimiter, $items);

                            } elseif (($default = intval($default)) > 0) {
                                $return[$key] = (string)$default;
                            }

                        } else {
                            $item = intval($item);

                            if ($item > 0) {
                                $return[$key] = $item;

                                // Если указали проверять все значения на уникальность, записываем текущие значения в список всех данных
                                if ($unique == 'all' && ! in_array($item, $tmp)) {
                                    $tmp[] = $item;
                                }

                            } elseif (($default = intval($default)) > 0) {
                                $return[$key] = $default;
                            }
                        }
                    }
                }

                if (count($return)) {
                    $return = array_values($return);
                }
            }

        } else {
            if (gettype($data) === 'integer') {
                $return = $data > 0 ? $data : intval($default);

                // Если указали ключ проверки всех значений на уникальность
                if ($unique == 'all' && ! in_array($return, $tmp)) {
                    $tmp[] = $return;
                }

            } elseif (! empty($data)) {
                $return = static::getIds([$data], $default, $delimiter, $unique, $recursive, $tmp);

                // Поскольку указали проверку по массиву,
                // на выходе то-же будет массив и надо делать проверку на наличие разделителя ( on может быть NULL )
                if (is_array($return)) {
                    $return = is_null($delimiter) ? join('', $return) : join($delimiter, $return);
                }

            } else {
                $return = intval($default) > 0 ? (int)$default : '';
            }
        }

        return $return;
    }


    /**
     * Проверка строку или массива строк в которых содержатся теги с указанным разделителем.
     *
     * @param string|null $delimiter - разделитель строки
     * @param string      $unique    - флаг для проверки значений на уникальное повторение
     * @param bool        $recursive - флаг для обхода потомков
     * @return $this
     */
    public function tags($delimiter = ',', $unique = 'single', $recursive = false)
    {
        $this->data = static::getTags($this->data, $this->getDefault(), $delimiter, $unique, $recursive);

        return $this;
    }


    /**
     * Проверка строку или массива строк в которых содержатся теги с указанным разделителем.
     *
     * @param array|string $data      - значения
     * @param mixed        $default   - значение по умолчанию
     * @param string|null  $delimiter - разделитель строки
     * @param string       $unique    - флаг для проверки значений на уникальное повторение ( в рамках одной строки )
     * @param bool         $recursive - флаг для обхода потомков
     * @param array        $tmp
     * @return array|string
     */
    static function getTags(
        $data,
        $default = null,
        $delimiter = ',',
        $unique = 'single',
        $recursive = false,
        &$tmp = []
    ) {
        if (is_array($data) && is_array($return = [])) {

            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive === true && is_array($item)) {
                        $current = [];
                        $return[$key] = static::getTags($item, $default, $delimiter, $unique, $recursive, $current);

                    } else {
                        $item = strval($item);

                        // логика разбития строки массива по разделителю
                        if (! empty($delimiter) && ! empty($item)) {
                            $current = [];
                            $return[$key] = static::getTags($item, $default, $delimiter, $unique, $recursive, $current);
                            continue;

                        }

                        if (empty($item)) {
                            continue;
                        }

                        if ($unique === 'single' && in_array($item, $tmp)) {
                            continue;
                        }

                        $tmp[] = $return[$key] = $item; // Записываем
                    }
                }

                if (count($return) > 0) {
                    $return = array_values($return);
                }
            }

        } else {
            $data = VarStr::explode(",", strval($data), ['']);

            foreach ($data as $key => $str) {
                if (empty($str)) {
                    unset($data[$key]);
                    continue;
                }

                if ($unique === 'single' && in_array($str, $tmp)) {
                    unset($data[$key]);
                    continue;
                }

                $tmp[] = $str; // Записываем
            }

            $return = count($data) > 0 ? join($delimiter, $data) : $default;
        }

        return $return;
    }
}