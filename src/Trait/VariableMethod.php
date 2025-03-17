<?php

namespace Warkhosh\Variable\Trait;

use Warkhosh\Variable\VarArray;
use Warkhosh\Variable\VarFloat;
use Warkhosh\Variable\Option\VarOptions;
use Warkhosh\Variable\VarInt;
use Warkhosh\Variable\VarStr;
use Exception;

/**
 * Trait VariableMethod
 *
 * $data - Это база значения переменной или переменных.
 * Если в начале преобразовать эту переменную в нужный тип (массив | строку)
 * последующие методы будет автоматически выполнять под этот тип алгоритмы!
 * Так мы сразу определяем тип значения переменной (массив или все остальное)
 *
 * @method static $this list()
 * @method static $this string()
 * @method static $this number()
 * @method static $this double()
 *
 * @package Ekv\Framework\Components\Support\Traits
 */
trait VariableMethod
{
    use VariableData;
    use VariableExtendedMethod;

    /**
     * Перезаписывает default значения.
     *
     * @note значения по умолчанию устанавливаются в конструкторе, но порой алгоритмы по мере работы могут потребовать изменить значения
     *
     * @param array|float|int|string|null $default
     * @return $this
     */
    public function byDefault(array|float|int|string|null $default): static
    {
        $this->default = $default;

        return $this;
    }

    /**
     * Возвращает значения по умолчанию
     *
     * @param string|null $key
     * @return array|float|int|string|null
     */
    public function getDefault(?string $key = null): array|float|int|string|null
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
     * Объявление типа переменной с преобразованием в неё если она не такая
     *
     * @param string $type
     * @param array $arguments
     * @return void
     * @throws Exception
     * @deprecated теперь работа с типами больше не требует этих методов
     */
    //public function setType(string $type = 'data', array $arguments = []): void
    //{
    //    if ($type === 'list') {
    //        $this->dataType = 'array'; // для определения как отдавать значения
    //
    //        if (! is_array($this->data)) {
    //            // Что-бы преобразование из текста сработало и первым элементом массива стал полученный текст,
    //            // нужен не пустой разделитель. Делаем разделитель запятая по умолчанию
    //            $delimiter = key_exists(0, $arguments) ? (string)$arguments[0] : ',';
    //            $this->makeArray($delimiter);
    //            $this->toArray($delimiter);
    //        }
    //    }
    //
    //    if ($type === 'string' || $type === 'number') {
    //        $this->dataType = 'value'; // для определения как отдавать значения
    //
    //        if (is_array($this->data)) {
    //            $delimiter = key_exists(0, $arguments) ? (string)$arguments[0] : ',';
    //            $this->toString($this->data, $delimiter);
    //        }
    //    }
    //
    //    if ($type === 'double') {
    //        $this->dataType = 'value'; // для определения как отдавать значения
    //
    //        if (is_array($this->data)) {
    //            $this->data = VarArray::getFirst($this->data);
    //        }
    //
    //        $this->data = floatval($this->data);
    //    }
    //}

    /**
     * Магический метод для реализации вызова зарезервированных методов как функций
     *
     * @param string $name
     * @param array $arguments
     * @return $this
     * @throws Exception
     * @deprecated теперь работа с типами больше не требует этих методов
     */
    //public function __call(string $name, array $arguments): static
    //{
    //    switch ($name) {
    //        case "list":
    //        case "string":
    //        case "integer":
    //            //case "float":
    //        case "double":
    //            $this->setType($name, $arguments);
    //
    //            return $this;
    //    }
    //
    //    //Log::error('Call of unknown method name: ' . $name . " in class: " . get_called_class());
    //    return $this;
    //}

    /**
     * Преобразование значения `$this->data` в массив!
     * Для последующих операций над переменной именно по сценарию массивов что-бы вернуть массив.
     *
     * @note: Если передать второй аргумент то он будет значением по умолчанию
     *
     * @param string|null $delimiter
     * @return $this
     * @throws Exception
     * @deprecated в 90% эта функция заменяется makeArray()
     */
    //public function toArray(?string $delimiter = null): static
    //{
    //    $default = count(func_get_args()) === 2 ? func_get_arg(1) : $this->getDefault();
    //
    //    // Проверка типа только для строк, чисел, float, поскольку у нас допускается когда тип данных указан как массив, а default значение integer
    //    // К примеру get('status_id', -1, 'array')->getInteger('filter');
    //    if ($this->dataType !== 'array') {
    //        if (! (is_null($default) || is_array($default))) {
    //            throw new Exception("Default values are not an array..");
    //        }
    //    }
    //
    //    if (gettype($this->data) == 'string') {
    //        $this->data = static::getToArray($this->data, $delimiter, (array)$default);
    //    } else {
    //        $this->data = (array)$this->data;
    //    }
    //
    //    return $this;
    //}

    /**
     * Преобразует и возвращает переданные значения в тип массив!
     * Для последующих операций над переменной именно по сценарию массивов что-бы вернуть массив.
     *
     * @param string|null $delimiter
     * @param array|float|int|string|null $data
     * @param array $default
     * @return array
     */
    //public static function getToArray(
    //    array|float|int|string|null $data,
    //    ?string $delimiter = null,
    //    array $default = []
    //): array {
    //    if (is_array($data)) {
    //        return $data;
    //    }
    //
    //    if (is_numeric($data)) {
    //        return [$data];
    //    }
    //
    //    if (is_string($data)) {
    //        if (is_string($delimiter) && mb_strlen($delimiter) > 0) {
    //            return explode($delimiter, $data);
    //        }
    //
    //        return empty($data) ? $default : [$data];
    //    }
    //
    //    return $default;
    //}

    /**
     * Преобразование значения $this->data` в строку!
     * Для последующих операций над переменной именно по сценарию строчного что-бы вернуть одно значение переменной.
     *
     * @note: Если передать второй аргумент то он будет значением по умолчанию
     *
     * @param string|null $delimiter
     * @return $this
     * @throws Exception
     */
    //public function toString(?string $delimiter = null): static
    //{
    //    $default = count(func_get_args()) === 2 ? func_get_arg(1) : $this->getDefault();
    //
    //    // Проверка типа только для строк, чисел, float, поскольку у нас допускается когда тип данных указан как массив, а default значение integer
    //    // К примеру get('status_id', -1, 'array')->getInteger('filter');
    //    if ($this->dataType !== 'array') {
    //        if (! (is_null($default) || is_string($default) || is_numeric($default))) {
    //            throw new Exception("Default values are not an string");
    //        }
    //    }
    //
    //    $this->data = static::getToString($this->data, $delimiter, $default);
    //
    //    return $this;
    //}

    /**
     * Преобразование значения переменной в тип = строчный!
     * Для последующих операций над переменной именно по сценарию строчного что-бы вернуть одно значение переменной.
     *
     * @param array|float|int|string|null $data
     * @param string|null $delimiter
     * @param string $default
     * @return string
     */
    //public static function getToString(
    //    array|float|int|string|null $data,
    //    ?string $delimiter = null,
    //    string $default = ''
    //): string {
    //    if (is_string($data)) {
    //        return $data;
    //    }
    //
    //    if (is_numeric($data)) {
    //        return (string)$data;
    //    }
    //
    //    if (is_array($data)) {
    //        if (is_string($delimiter) && mb_strlen($delimiter) > 0) {
    //            return join($delimiter, $data);
    //        }
    //
    //        return count($data) === 0 ? $default : join("", $data);
    //    }
    //
    //    return $default;
    //}

    /**
     * Возвращает преобразованное значение(я) в строку в зависимости от указанного алгоритма
     *
     * @note с версии 1.1 добавлены методы getArray() и array(),
     * в этом методе скоро будет возвращаться строго один тип `string`, а для array используйте новые методы!
     *
     * @param string $option
     * @param bool $recursive флаг для обхода потомков
     * @param array $remove символы для удаления из текста
     * @return array|string
     * @throws Exception
     * @version 1.1
     */
    public function getInput(
        string $option = 'small',
        bool $recursive = false,
        array $remove = ["\t", "\n", "\r"]
    ): array|string {
        return $this->input($option, $recursive, $remove)->get();
    }

    /**
     * Преобразование значений в строку(и) в зависимости от указанного алгоритма
     *
     * @param string $option
     * @param bool $recursive флаг для обхода потомков
     * @param array $remove символы для удаления из текста
     * @return $this
     * @throws Exception
     * @version 1.1
     */
    public function input(string $option = 'small', bool $recursive = false, array $remove = ["\t", "\n", "\r"]): static
    {
        //$this->makeString($recursive);

        if ($option === 'unchanged' || $option === 'raw') {
            return $this;
        }

        if ($option === "tags" && in_array(",", $remove)) {
            $index = array_search(",", $remove);

            if ($index !== false) {
                unset($remove[$index]);
            }
        }

        switch ($option) {
            /**
             * Преобразование значений в строку формата price без копеек
             */
            case 'price':
                return $this->crop(25, $recursive)
                    ->stringWithGreaterZero("auto", 0, $recursive)
                    ->numberFormat(0, '.', '', $recursive);

            case 'price-upward':
                return $this->crop(25, $recursive)
                    ->stringWithGreaterZero("upward", 0, $recursive)
                    ->numberFormat(0, '.', '', $recursive);

            case 'price-downward':
                return $this->crop(25, $recursive)
                    ->stringWithGreaterZero("downward", 0, $recursive)
                    ->numberFormat(0, '.', '', $recursive);

                /**
                 * Преобразование значений в строку формата price с указанием копеек
                 */
            case 'cost':
                return $this->crop(25, $recursive)
                    ->stringWithGreaterZero("auto", 2, $recursive)
                    ->numberFormat(2, '.', '', $recursive);

            case 'cost-upward':
                return $this->crop(25, $recursive)
                    ->stringWithGreaterZero("upward", 2, $recursive)
                    ->numberFormat(2, '.', '', $recursive);

            case 'cost-downward':
                return $this->crop(25, $recursive)
                    ->stringWithGreaterZero("downward", 2, $recursive)
                    ->numberFormat(2, '.', '', $recursive);

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
                $this->trim(VarOptions::TRIM_REMOVE_CHAR, $recursive);

                if (is_array($remove) && count($remove) > 0) {
                    $this->removeSymbol($remove, $recursive);
                }

                // кодирует символы в сущности
                if ($option === 'tags-html-encode'
                    || $option === 'xs-html-encode'
                    || $option === 'sm-html-encode'
                    || $option === 'small-html-encode'
                    || $option === 'text-html-encode'
                    || $option === 'mediumtext-html-encode'
                    || $option === 'longtext-html-encode') {
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

                if ($option == 'mediumtext-html-decode'.'tags-html-decode') { // кодирует сущности в символы
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
                 * Для проверки фильтров, где допускаются значения от -1,0,1,2,3,4,5 ...
                 *
                 * @note -1 не определено, 0 не выбрано, 1,2,3,4,5 ...
                 */
            case 'filter':
                $this->filter(',', 'all');
                break;

            case 'options':
                /**
                 * Проверяет и оставляет в строке или массиве наличие только чисел.
                 * Для строк будет дополнительная операция по разделению её на части через $delimiter в виде запятой.
                 * Все числа проходят проверки на целое положительно число!
                 * 0,1,2,3,4,5 ...
                 *
                 * @note если вам надо сохранить порядок в текстовом списке значений, используйте input("text") и далее сами преобразовывайте сами
                 */
                $this->options(',', 0)->removeItems(['', null]);
                break;

                /**
                 * Проверяет и оставляет в строке или массиве наличие только чисел.
                 * Для строк будет дополнительная операция по разделению её на части через $delimiter в виде запятой.
                 * Все значения проходят проверки на целое положительно число больше нуля!
                 * 1,2,3,4,5 ...
                 *
                 * @note если вам надо сохранить порядок в текстовом списке значений, используйте input("text") и далее сами преобразовывайте сами
                 */
            case 'ids':
                $this->ids(',', 'all')->removeItems();
                break;
        }

        if ($option === "tags") {
            $this->tags();
        }

        return $this;
    }

    /**
     * Возвращает преобразованное значение(я) в число с плавающей точкой в зависимости от указанного алгоритма
     *
     * @note с версии 1.1 добавлены методы getArray() и array(),
     * в этом методе скоро будет возвращаться строго один тип `string`, а для array используйте новые методы!
     *
     * @param int|string $option если указали число считаем его определением точности (decimals)
     * @param string $round тип округления (auto, upward, downward)
     * @param bool $positive флаг положительного числа
     * @param bool $recursive флаг для обхода потомков
     * @return array|float
     * @throws Exception
     * @version 1.1
     */
    public function getFloat(
        int|string $option,
        string $round = "auto",
        bool $positive = false,
        bool $recursive = false
    ): array|float {
        return $this->float($option, $round, $positive, $recursive)->get();
    }

    /**
     * Преобразование значений в число(а) с плавающей точкой в зависимости от указанного алгоритма
     *
     * @param int|string $option если указали число считаем его определением точности (decimals)
     * @param string $round тип округления десятичного значения (auto, upward, downward)
     * @param bool $positive флаг положительного числа
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     * @throws Exception
     * @version 1.1
     */
    public function float(
        int|string $option = 12,
        string $round = "auto",
        bool $positive = false,
        bool $recursive = false
    ): static {
        // Преобразование значений в денежную единицу без копеек
        if ($option === 'price') {
            return $this->makeFloat(0, "auto", true, $recursive);
        } elseif ($option === 'price-upward') {
            return $this->makeFloat(0, "upward", true, $recursive);
        } elseif ($option === 'price-downward') {
            return $this->makeFloat(0, "downward", true, $recursive);
        }

        // Преобразование значений в денежную единицу с учетом копеек
        if ($option === 'cost') {
            return $this->makeFloat(2, "auto", true, $recursive);
        } elseif ($option === 'cost-upward') {
            return $this->makeFloat(2, "upward", true, $recursive);
        } elseif ($option === 'cost-downward') {
            return $this->makeFloat(2, "downward", true, $recursive);
        }

        // если указали число считаем это за определение точности
        $decimals = is_int($option) && $option >= 0 ? $option : 12;

        return $this->makeFloat($decimals, $round, $positive, $recursive);
    }

    /**
     * Возвращает преобразованное значение(я) в целые числа в зависимости от указанного алгоритма
     *
     * @note с версии 1.1 добавлены методы getArray() и array(),
     * в этом методе скоро будет возвращаться строго один тип `string`, а для array используйте новые методы!
     *
     * @param string $option
     * @param bool $positive
     * @param bool $recursive флаг для обхода потомков
     * @return array|int
     * @throws Exception
     * @version 1.1
     */
    public function getInteger(string $option, bool $positive = false, bool $recursive = false): array|int
    {
        return $this->integer($option, $positive, $recursive)->get();
    }

    /**
     * Преобразование значений в целое число(а) в зависимости от указанного алгоритма
     * Сокращённый метод работы с цифрами
     *
     * @param string $option
     * @param bool $positive
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     * @throws Exception
     * @version 1.1
     */
    public function integer(string $option, bool $positive = false, bool $recursive = false): static
    {
        // для работы логики filter принудительно указываем флаг положительного числа в FALSE
        if ($option === 'filter') {
            $positive = false;
        }

        // для переключателя делаем условие определения значения более мягкие и разрешаем on|yes|true ...
        $strict = ! in_array($option, ['toggle', 'ids']);

        // Отдельный алгоритм для исходных данных если идёт обработка сценария работы с ценой
        if (in_array($option, ['price', 'price-upward', 'price-downward'])) {
            //$round = $option === "price" ? "auto" : ($option === "price-upward" ? "upward" : "downward");
            //$this->makeFloat(0, $round);
        } else {
            //$this->makeInteger($positive, $recursive, $strict);
        }

        /**
         * Для проверки фильтров, где допускаются значения от -1,0,1,2,3,4,5 ...
         *
         * @note -1 не определено, 0 не выбрано, 1,2,3,4,5 ...
         */
        if ($option === 'filter') {
            $this->minInteger(-1, -1, $recursive);
        }

        switch ($option) {
            /**
             * Для проверки списков, где допускаются значения от 0 (не выбрано), 1, 2, 3, 4, 5 ...
             */
            case 'option':
            case 'price': // цена без копеек
                $this->minInteger(0, 0, $recursive)
                    ->makeFloat(0, "auto", true, $recursive)
                    ->makeInteger(true, $recursive, false);
                break;

            case 'price-upward':
                $this->minInteger(0, 0, $recursive)
                    ->makeFloat(0, "upward", true, $recursive)
                    ->makeInteger(true, $recursive, false);
                break;

            case 'price-downward':
                $this->minInteger(0, 0, $recursive)
                    ->makeFloat(0, "downward", true, $recursive)
                    ->makeInteger(true, $recursive, false);
                break;

                /**
                 * Преобразование значений в денежную единицу с учетом копеек не укладывается в тип integer
                 */
                //case 'cost':
                //    return $this->minInteger(0, 0, $recursive)
                //        ->makeFloat(2, "auto", true, $recursive)
                //        ->makeInteger(true, $recursive, false);

                /**
                 * Для проверки значения на ID, в массивах 0 будет удалён, а вот для единственного значения 0 будет заменён на default
                 */
            case 'id':
                $this->minInteger(0, 0, $recursive)
                    ->removeItems()
                    ->makeInteger(true, $recursive, false);
                break;

            case 'year':
                $this->byDefault(1970)
                    ->makeInteger(true, $recursive)
                    ->minInteger($this->getDefault(), 1970, $recursive);
                break;

                /**
                 * Для пагинатора не допускаем значение страницы ниже один!
                 */
            case 'page':
            case 'pagination':
                $this->makeInteger(true, $recursive)->minInteger(1, 1, $recursive);

                // Бросаем исключение если были не корректные параметры
                if ($this->getDefault() <= 0) {
                    throw new Exception('The default value for the page must be a number greater than zero');
                }

                break;

                /**
                 * Для переключателей нужно всего 2 значения 0) off и 1) on
                 * Не устанавливаем принудительное значение по умолчанию что-бы иметь возможность гибко настроить поведение!
                 */
            case 'toggle':
                $this->minInteger(0, 0, $recursive)
                    ->maxInteger(1, 1, $recursive);
                break;

            case 'ids':
                // Для числа не следует делать этот алгоритм, а сразу в коде сделать проверку на больше или равно
                throw new Exception('This algorithm does not work for the number type.');
        }

        return $this;
    }

    /**
     * Возвращает преобразованное значение(я) в массив в зависимости от указанного алгоритма
     *
     * @param string $option
     * @param string|null $delimiter
     * @param bool $recursive
     * @return array
     * @throws Exception
     * @version 1.1
     */
    public function getArray(string $option, ?string $delimiter = null, bool $recursive = false): array
    {
        return $this->array($option, $delimiter, $recursive)->get();
    }

    /**
     * Преобразование значений в массив в зависимости от указанного алгоритма
     * Сокращённый метод работы с цифрами
     *
     * @param string $option
     * @param string $delimiter
     * @param bool $recursive
     * @return $this
     * @throws Exception
     * @version 1.1
     */
    public function array(string $option, string $delimiter = ',', bool $recursive = false): static
    {
        switch ($option) {
            /**
             * Проверка значений массива на наличие идентификатора(ов).
             * Идентификаторы проходят проверки на целое положительно число (больше нуля).
             *
             * @note Если проверяемая переменная содержала строку,
             * она будет преобразована в число по правилам строгой типизации и только после будет произведена проверка на ID!
             * @note Для работы со списками из строк, работайте с методом input('ids');
             *
             * 1,2,3,4,5 ...
             */
            case 'ids':
            case 'id':
                $this->ids($delimiter, 'all', $recursive);
                break;
        }

        return $this;
    }

    /**
     * Устанавливает во что преобразовывать пустую строку(и) если они получились при конвертации
     *
     * @param string $var
     * @return $this
     */
    public function convertEmptyString(string $var): static
    {
        $this->convertAnEmptyString = $var;

        return $this;
    }

    /**
     * Преобразование значения(й) `$this->data` в строку
     *
     * @param bool $recursive
     * @return $this
     * @throws Exception
     */
    private function makeString(bool $recursive = false): static
    {
        $default = $this->getDefault();

        // Проверка типа только для строк, чисел, float, поскольку у нас допускается когда тип данных указан как массив, а default значение integer
        // К примеру get('status_id', -1, 'array')->getInteger('filter');
        if ($this->dataType !== 'array') {
            if (! (is_null($default) || is_string($default) || is_numeric($default))) {
                throw new Exception("Default values are not an string");
            }
        }

        $this->data = static::getMakeString($this->data, (string)$default, $recursive);

        return $this;
    }

    /**
     * Преобразование переданного значения(й) в строку
     *
     * @param array|bool|float|int|string|null $data
     * @param string $default
     * @param bool $recursive
     * @return array|string
     */
    private static function getMakeString(
        array|bool|float|int|string|null $data,
        string $default = '',
        bool $recursive = false
    ): array|string {
        if (is_array($data) && is_array($return = [])) {

            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getMakeString($item, $default, $recursive);

                    } else {
                        $return[$key] = is_null($item) ? $default : VarStr::getMake($item);
                    }
                }
            }

        } else {
            // VarStr::getMake() преобразовывает bool значение в слова true/false и поэтому мыв этот тип данных раньше конвертируем
            $data = is_bool($data) ? $default : $data;
            $return = is_null($data) ? $default : VarStr::getMake($data);
        }

        return $return;
    }

    /**
     * Преобразование значения(й) `$this->data` в число с плавающей точкой
     *
     * @note: возможны отрицательные значения!
     *
     * @param int $decimals точность (символы после точки)
     * @param string $round тип округления десятичного значения (auto, upward, downward)
     * @param bool $positive флаг положительного числа, >= 0
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     * @throws Exception
     */
    private function makeFloat(
        int $decimals = 2,
        string $round = "auto",
        bool $positive = true,
        bool $recursive = false
    ): static {
        $default = $this->getDefault();

        // Проверка типа только для строк, чисел, float, поскольку у нас допускается когда тип данных указан как массив, а default значение integer
        // К примеру get('status_id', -1, 'array')->getInteger('filter');
        if ($this->dataType !== 'array') {
            if (! (is_null($default) || is_numeric($default) || VarFloat::isStringOnFloat($default))) {
                throw new Exception("Default values are not an float");
            }
        }

        $default = VarFloat::getMake($default);
        $this->data = static::getMakeFloat($this->data, $decimals, $round, $default, $positive, $recursive);

        return $this;
    }

    /**
     * Преобразование переданного значения(й) в число с плавающей точкой
     *
     * @note: возможны отрицательные значения!
     *
     * @param array|bool|float|int|string|null $data
     * @param int $decimals точность (символы после точки)
     * @param string $round тип округления десятичного значения (auto, upward, downward)
     * @param float $default
     * @param bool $positive флаг положительного числа, >= 0
     * @param bool $recursive флаг для обхода потомков
     * @return array|float
     * @throws Exception
     */
    private static function getMakeFloat(
        array|bool|float|int|string|null $data,
        int $decimals = 2,
        string $round = "auto",
        float $default = 0.0,
        bool $positive = true,
        bool $recursive = false
    ): array|float {
        if ($positive && $default < 0) {
            throw new Exception("Default values are not positive");
        }

        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {

                    if ($recursive && is_array($item)) {
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
     * Преобразование значения `$this->data` в целое число
     *
     * @note: возможны отрицательные значения!
     *
     * @param bool $positive флаг положительного числа, >= 0
     * @param bool $recursive флаг для обхода потомков
     * @param bool $strict флаг для преобразования дополнительных значений типа "on|off|no|yes" в число
     * @return $this
     * @throws Exception
     */
    private function makeInteger(bool $positive = true, bool $recursive = false, bool $strict = false): static
    {
        $default = $this->getDefault();

        // Проверка типа только для строк, чисел, float, поскольку у нас допускается когда тип данных указан как массив, а default значение integer
        // К примеру get('status_id', -1, 'array')->getInteger('filter');
        if ($this->dataType !== 'array') {
            if (! (is_null($default) || is_numeric($default))) {
                throw new Exception("Default values are not an number2");
            }
        }

        $this->data = static::getMakeInteger($this->data, (int)$default, $positive, $recursive, $strict);

        return $this;
    }

    /**
     * Преобразование переданного значения(й) в целое число
     *
     * @note: возможны отрицательные значения!
     *
     * @param array|bool|float|int|string|null $data
     * @param int $default
     * @param bool $positive флаг положительного числа, >= 0
     * @param bool $recursive флаг для обхода потомков
     * @param bool $strict флаг для преобразования дополнительных значений типа "on|off|no|yes" в число
     * @return array|int
     * @throws Exception
     */
    private static function getMakeInteger(
        array|bool|float|int|string|null $data,
        int $default = 0,
        bool $positive = true,
        bool $recursive = false,
        bool $strict = false
    ): array|int {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {

                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getMakeInteger($item, $default, $positive, $recursive);

                    } else {
                        if ($positive) {
                            $return[$key] = VarInt::getMakePositiveInteger($item, $default, $strict);
                        } else {
                            $return[$key] = VarInt::getMake($item, $default, $strict);
                        }
                    }
                }
            }

        } else {
            // Сбрасываем пустые данные до NULL, при конвертации это позволит более корректно установить $default
            $data = $data === '' ? null : $data;

            if ($positive) {
                $return = VarInt::getMakePositiveInteger($data, $default, $strict);
            } else {
                $return = VarInt::getMake($data, $default, $strict);
            }
        }

        return $return;
    }

    /**
     * Преобразование значения(й) `$this->data` в массив
     *
     * @param string|null $delimiter
     * @return $this
     * @throws Exception
     */
    private function makeArray(?string $delimiter = null): static
    {
        $default = $this->getDefault();

        // Проверка когда тип данных указан как массив, а default значение integer
        // К примеру get('status_id', -1, 'array')->getInteger('filter');
        if (! is_array($default)) {
            throw new Exception("Default values are not an array.");
        }

        $this->data = static::getMakeArray($this->data, $delimiter, (array)$default);

        return $this;
    }

    /**
     * Преобразование переданного значения(й) в строку
     *
     * @param array|bool|float|int|string|null $data
     * @param string|null $delimiter
     * @param array $default
     * @return array|string
     */
    private static function getMakeArray(
        array|bool|float|int|string|null $data,
        ?string $delimiter = null,
        array $default = []
    ): array|string {
        if (is_bool($data) || is_null($data)) {
            return $default;
        }

        if (is_array($data)) {
            return $data;
        }

        if (is_numeric($data) || is_float($data)) {
            return [$data];
        }

        if (is_string($delimiter) && mb_strlen($delimiter) > 0) {
            return explode($delimiter, $data);
        }

        return $default;
    }

    /**
     * Возвращает преобразованное значение(я) в целое положительное число (денежную единицу без копеек)
     *
     * @note в методе нет аргумента $decimals поскольку price это всегда целое число у которого нет чисел после точки!
     *
     * @param string $round тип округления десятичного значения (auto, upward, downward)
     * @param bool $recursive флаг для обхода потомков
     * @return array|int
     * @throws Exception
     * @deprecated Метод больше не поддерживается!!!
     */
    public function getPrice(string $round = "auto", bool $recursive = false): array|int
    {
        throw new Exception(
            "Метод больше не поддерживается, используйте базовые input(), getInput(), float(), getFloat(), integer(), getInteger()"
        );
        //$default = $this->getDefault();

        // Проверка типа только для строк, чисел, float, поскольку у нас допускается когда тип данных указан как массив, а default значение integer
        // К примеру get('status_id', -1, 'array')->getInteger('filter');
        //if ($this->dataType !== 'array') {
        //    if (! (is_null($default) || is_numeric($default))) {
        //        throw new Exception("Default values are not an number3");
        //    }
        //}

        //$data = $this->input('sm')->pregReplace("/[^0-9\.\,]/", "")->get();

        //return static::getMakePrice($data, (int)$default, $recursive, $round);
    }

    /**
     * Преобразование значения(й) в целое положительное число (денежную единицу без копеек)
     *
     * @note в методе нет аргумента $decimals поскольку price это всегда целое число у которого нет чисел после точки!
     *
     * @param array|float|int|string|null $data
     * @param int $default
     * @param bool $recursive флаг для обхода потомков
     * @param string $round тип округления десятичного значения (auto, upward, downward)
     * @return array|int
     * @throws Exception
     * @deprecated Метод больше не поддерживается!!!
     */
    public static function getMakePrice(
        array|float|int|string|null $data,
        int $default,
        bool $recursive = false,
        string $round = "auto"
    ): array|int {
        throw new Exception(
            "Метод больше не поддерживается, используйте базовые input(), getInput(), float(), getFloat(), integer(), getInteger()"
        );

        //if (is_array($data) && is_array($return = [])) {
        //    if (count($data) > 0) {
        //        reset($data);
        //
        //        foreach ($data as $key => $item) {
        //            if ($recursive && is_array($item)) {
        //                $return[$key] = static::getMakePrice($item, $default, $recursive, $round);
        //
        //            } else {
        //                $item = is_string($item) ? $item : VarStr::getMake($item);
        //                $return[$key] = (int)VarStr::getNumberFormat($item, $default, false, $round);
        //            }
        //        }
        //    }
        //
        //} else {
        //    $data = is_string($data) ? $data : VarStr::getMake($data);
        //
        //    // Всегда преобразуем строку в float, на случай если передали значения не по типу
        //    $cost = static::getMakeFloat($data, 0, $round, (float)$default, true);
        //    $return = (int)VarStr::getNumberFormat($cost, 0, '', '', $default);
        //}
        //
        //return $return;
    }

    /**
     * Возвращает преобразованное значение(я) в целое положительное число (денежную единицу с копейками)
     *
     * @param string $round тип округления десятичного значения (auto, upward, downward)
     * @param int $decimals точность (символы после точки)
     * @param string $separator разделитель точности
     * @param bool $recursive флаг для обхода потомков
     * @return array|float
     * @throws Exception
     * @deprecated Метод больше не поддерживается!!!
     */
    public function getCost(
        string $round = "auto",
        int $decimals = 2,
        string $separator = '.',
        bool $recursive = false
    ): array|float {
        throw new Exception(
            "Метод больше не поддерживается, используйте базовые input(), getInput(), float(), getFloat(), integer(), getInteger()"
        );

        //$default = $this->getDefault();
        //
        // Проверка типа только для строк, чисел, float, поскольку у нас допускается когда тип данных указан как массив, а default значение integer
        // К примеру get('status_id', -1, 'array')->getInteger('filter');
        //if ($this->dataType !== 'array') {
        //    if (! (is_null($default) || is_numeric($default) || VarFloat::isStringOnFloat($default))) {
        //        throw new Exception("Default values are not an float");
        //    }
        //}
        //
        //$default = VarFloat::getMake($default);
        //$data = $this->input('sm')->pregReplace("/[^0-9\.\,]/", "")->get();
        //
        //return static::getMakeCost($data, $default, $recursive, $decimals, $round, $separator);
    }

    /**
     * Преобразование значения(й) в целое положительное число (денежную единицу с копейками)
     *
     * @param array|float|int|string|null $data
     * @param float $default
     * @param bool $recursive флаг для обхода потомков
     * @param int $decimals точность (символы после точки)
     * @param string $round тип округления десятичного значения (auto, upward, downward)
     * @param string $separator разделитель точности
     * @return array|float
     * @throws Exception
     * @deprecated Метод больше не поддерживается!!!
     */
    public static function getMakeCost(
        array|float|int|string|null $data,
        float $default,
        bool $recursive = false,
        int $decimals = 2,
        string $round = "auto",
        string $separator = '.'
    ): array|float {
        throw new Exception(
            "Метод больше не поддерживается, используйте базовые input(), getInput(), float(), getFloat(), integer(), getInteger()"
        );
    }

    /**
     * Преобразование значения(й) в целое число с проверкой, что оно не ниже указанного минимального значения
     *
     * @note возможны отрицательные значения!
     *
     * @param int $min число для проверки
     * @param int $default
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     * @throws Exception
     */
    public function minInteger(int $min = 0, int $default = 0, bool $recursive = false): static
    {
        $this->data = static::getMinInteger($this->data, $min, $default, $recursive);

        return $this;
    }

    /**
     * Возвращает преобразованное значение(я) в целое число с проверкой, что оно не ниже указанного минимального значения
     *
     * @note возможны отрицательные значения!
     *
     * @param array|float|int|string|null $data
     * @param int $min число для проверки
     * @param int $default
     * @param bool $recursive флаг для обхода потомков
     * @return array|int
     */
    public static function getMinInteger(
        array|float|int|string|null $data,
        int $min = 0,
        int $default = 0,
        bool $recursive = false
    ): array|int {
        if (is_array($data) && is_array($return = [])) {
            reset($data);

            foreach ($data as $key => $item) {
                if ($recursive && is_array($item)) {
                    $return[$key] = static::getMinInteger($item, $min, $default, $recursive);

                } else {
                    $int = VarInt::getMake($item, $default);
                    $return[$key] = $int >= $min ? $int : $default;
                }
            }

        } else {
            $int = VarInt::getMake($data, $default);
            $return = $int >= $min ? $int : $default;
        }

        return $return;
    }

    /**
     *  Преобразование значения(й) в целое число с проверкой, что оно не выше указанного минимального значения
     *
     * @note: возможны отрицательные значения!
     *
     * @param int $max
     * @param int $default
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     * @throws Exception
     */
    public function maxInteger(int $max = 0, int $default = 1, bool $recursive = false): static
    {
        $this->data = static::getMaxInteger($this->data, $max, $default, $recursive);

        return $this;
    }

    /**
     * Возвращает преобразованное значение(я) в целое число с проверкой, что оно не выше указанного минимального значения
     *
     * @note: возможны отрицательные значения!
     *
     * @param array|float|int|string|null $data
     * @param int $max
     * @param int $default значение по умолчанию
     * @param bool $recursive флаг для обхода потомков
     * @return array|int
     */
    public static function getMaxInteger(
        array|float|int|string|null $data,
        int $max = 0,
        int $default = 1,
        bool $recursive = false
    ): array|int {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getMaxInteger($item, $max, $default, $recursive);

                    } else {
                        $int = VarInt::getMake($item, $default);
                        $return[$key] = $int <= $max ? $int : $default;
                    }
                }
            }
        } else {
            $int = VarInt::getMake($data, $default);
            $return = $int <= $max ? $int : $default;
        }

        return $return;
    }

    /**
     * Форматирует число с разделением групп
     *
     * @note результат работы будет в виде строки и нужно не забывать после привести к нужному типу!
     *
     * @param int $decimals точность (символы после точки)
     * @param string $separator разделитель точности
     * @param string $thousands_sep разделитель тысяч
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     * @throws Exception
     */
    public function numberFormat(
        int $decimals = 2,
        string $separator = '.',
        string $thousands_sep = '',
        bool $recursive = false
    ): static {
        $default = $this->getDefault();

        // Проверка типа только для строк, чисел, float, поскольку у нас допускается когда тип данных указан как массив, а default значение integer
        // К примеру get('status_id', -1, 'array')->getInteger('filter');
        if ($this->dataType !== 'array') {
            if (! (is_null($default) || is_numeric($default) || VarFloat::isStringOnFloat($default))) {
                throw new Exception("Default values are not an float");
            }
        }

        $default = VarFloat::getMake($default);
        $this->data = static::getNumberFormat($this->data, $decimals, $separator, $thousands_sep, $default, $recursive);

        return $this;
    }

    /**
     * Форматирует число с разделением групп
     *
     * @note не допускается значение в $decimals меньше нуля
     *
     * @param array|bool|float|int|string|null $data
     * @param int $decimals точность (символы после точки)
     * @param string $separator разделитель точности
     * @param string $thousands_sep разделитель тысяч
     * @param float|int $default
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     * @throws Exception
     */
    public static function getNumberFormat(
        array|bool|float|int|string|null $data,
        int $decimals = 2,
        string $separator = '.',
        string $thousands_sep = '',
        float|int $default = 0,
        bool $recursive = false
    ): array|string {
        $decimals = $decimals >= 0 ? $decimals : 2;

        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getNumberFormat($item, $recursive);

                    } else {
                        $item = is_string($item) ? $item : VarStr::getMake($item);

                        // Всегда преобразуем строку в float, на случай если передали значения не по типу
                        $cost = static::getMakeFloat($item, $decimals, "auto", (float)$default, true);
                        $return[$key] = VarStr::getNumberFormat($cost, $decimals, $separator, $thousands_sep, $default);
                    }
                }
            }
        } else {
            $data = is_string($data) ? $data : VarStr::getMake($data);

            // Всегда преобразуем строку в float, на случай если передали значения не по типу
            $cost = static::getMakeFloat($data, $decimals, "auto", (float)$default, true);
            $return = VarStr::getNumberFormat($cost, $decimals, $separator, $thousands_sep, $default);
        }

        return $return;
    }

    /**
     * Удаляет экранирование символов
     *
     * @param array|string $pattern искомый шаблон
     * @param array|string $replacement
     * @param bool $recursive флаг для обхода потомков
     * @param int $limit максимально возможное количество замен каждого шаблона для каждой строки subject. По умолчанию равно -1 (без ограничений)
     * @return $this
     */
    public function pregReplace(
        array|string $pattern = '',
        array|string $replacement = '',
        bool $recursive = false,
        int $limit = -1
    ): static {
        $this->data = static::getPregReplace($this->data, $pattern, $replacement, $recursive, $limit);

        return $this;
    }

    /**
     * Удаляет экранирование символов (статически метод для использования в контексте класса)
     *
     * @param array|bool|float|int|string|null $data
     * @param array|string $pattern искомый шаблон
     * @param array|string $replacement
     * @param bool $recursive флаг для обхода потомков
     * @param int $limit максимально возможное количество замен каждого шаблона для каждой строки subject. По умолчанию равно -1 (без ограничений)
     * @return array|string
     */
    public static function getPregReplace(
        array|bool|float|int|string|null $data,
        array|string $pattern = '',
        array|string $replacement = '',
        bool $recursive = false,
        int $limit = -1
    ): array|string {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getPregReplace($item, $pattern, $replacement, $recursive, $limit);

                    } else {
                        $item = is_string($item) ? $item : VarStr::getMake($item);
                        $return[$key] = preg_replace($pattern, $replacement, $item, $limit);
                    }
                }
            }

        } else {
            $data = is_string($data) ? $data : VarStr::getMake($data);
            $return = preg_replace($pattern, $replacement, $data, $limit);
        }

        return $return;
    }

    /**
     * Преобразует мнемоники (HTML-сущности) в специальные символы
     *
     * @note метод одноименный VarStr::getHtmlEntityDecode()
     *
     * @example: &amp; > & | &quot; > " | &bull; > •
     *
     * @param int $flags битовая маска из флагов определяющая режим обработки
     * @param string $encoding кодировка
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     */
    public function htmlEntityDecode(
        int $flags = ENT_COMPAT | ENT_HTML5,
        string $encoding = 'UTF-8',
        bool $recursive = false
    ): static {
        $this->data = static::getHtmlEntityDecode($this->data, $flags, $encoding, $recursive);

        return $this;
    }

    /**
     * Преобразует мнемоники (HTML-сущности) в специальные символы
     *
     * @note метод одноименный VarStr::getHtmlEntityDecode()
     *
     * @example: &amp; > & | &quot; > " | &bull; > •
     *
     * @param array|bool|float|int|string|null $data
     * @param int $flags битовая маска из флагов определяющая режим обработки
     * @param string $encoding кодировка
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     */
    public static function getHtmlEntityDecode(
        array|bool|float|int|string|null $data,
        int $flags = ENT_COMPAT | ENT_HTML5,
        string $encoding = 'UTF-8',
        bool $recursive = false
    ): array|string {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getHtmlEntityDecode($item, $flags, $encoding, $recursive);

                    } else {
                        $item = is_string($item) ? $item : VarStr::getMake($item);
                        $return[$key] = VarStr::getHtmlEntityDecode($item, $flags, $encoding);
                    }
                }
            }
        } else {
            $data = is_string($data) ? $data : VarStr::getMake($data);
            $return = VarStr::getHtmlEntityDecode($data, $flags, $encoding);
        }

        return $return;
    }

    /**
     * Преобразует специальные символы в соответствующие мнемоники (HTML-сущности)
     * Если надо преобразовать &copy; > &amp;copy; следует четвертый параметр $htmlEncode установить в TRUE
     *
     * @note для преобразования только символов &, ", ', <, > используйте ->htmlSpecialCharsEncode()
     * @note метод одноименный VarStr::getHtmlEntityEncode()
     *
     * @example & > &amp; | " > &quot; | ' > &apos; | > в &lt; | < в &gt;
     *
     * @param int $flags битовая маска из флагов определяющая режим обработки
     * @param string $encoding кодировка
     * @param bool $doubleEncode при выключении не будет преобразовывать существующие HTML-сущности. При включении приведет к преобразованию &copy; > &amp;copy;
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     */
    public function htmlEntityEncode(
        int $flags = ENT_COMPAT | ENT_HTML5,
        string $encoding = 'UTF-8',
        bool $doubleEncode = false,
        bool $recursive = false
    ): static {
        $this->data = static::getHtmlEntityEncode($this->data, $flags, $encoding, $doubleEncode, $recursive);

        return $this;
    }

    //4

    /**
     * Преобразует специальные символы в соответствующие мнемоники (HTML-сущности)
     * Если надо преобразовать &copy; > &amp;copy; следует четвертый параметр $htmlEncode установить в TRUE
     *
     * @note для преобразования только символов &, ", ', <, > используйте ->getHtmlSpecialCharsEncode()
     * @note метод одноименный VarStr::getHtmlEntityEncode()
     *
     * @example & > &amp; | " > &quot; | ' > &apos; | > в &lt; | < в &gt;
     *
     * @param array|bool|float|int|string|null $data
     * @param int $flags битовая маска из флагов определяющая режим обработки
     * @param string $encoding кодировка
     * @param bool $doubleEncode при выключении не будет преобразовывать существующие HTML-сущности. При включении приведет к преобразованию &copy; > &amp;copy;
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     */
    public static function getHtmlEntityEncode(
        array|bool|float|int|string|null $data,
        int $flags = ENT_COMPAT | ENT_HTML5,
        string $encoding = 'UTF-8',
        bool $doubleEncode = false,
        bool $recursive = false
    ): array|string {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getHtmlEntityEncode($item, $flags, $encoding, $doubleEncode, $recursive);

                    } else {
                        $item = is_string($item) ? $item : VarStr::getMake($item);
                        $return[$key] = VarStr::getHtmlEntityEncode($item, $flags, $encoding, $doubleEncode);
                    }
                }
            }
        } else {
            $data = is_string($data) ? $data : VarStr::getMake($data);
            $return = VarStr::getHtmlEntityEncode($data, $flags, $encoding, $doubleEncode);
        }

        return $return;
    }

    //5

    /**
     * Преобразует специальные символы (ограниченный набор) в соответствующие мнемоники (HTML-сущности)
     *
     * @note преобразует только символы &, ", ', <, >, для кодирования всех символов используйте ->htmlEntityEncode()
     * @note метод одноименный VarStr::getHtmlSpecialCharsEncode()
     *
     * @example & > &amp; | " > &quot; | ' > &apos; | > в &lt; | < в &gt;
     *
     * @param int $flags битовая маска из флагов определяющая режим обработки
     * @param string $encoding кодировка
     * @param bool $doubleEncode при выключении не будет преобразовывать существующие HTML-сущности. При включении приведет к преобразованию &amp; > &amp;amp;
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     */
    public function htmlSpecialCharsEncode(
        int $flags = ENT_COMPAT | ENT_HTML5,
        string $encoding = 'UTF-8',
        bool $doubleEncode = true,
        bool $recursive = false
    ): static {
        $this->data = static::getHtmlSpecialCharsEncode($this->data, $flags, $encoding, $doubleEncode, $recursive);

        return $this;
    }

    //6

    /**
     * Преобразует специальные символы (ограниченный набор) в соответствующие мнемоники (HTML-сущности)
     *
     * @note преобразует только символы &, ", ', <, >, для кодирования всех символов используйте ->getHtmlEntityEncode()
     * @note метод одноименный VarStr::getHtmlSpecialCharsEncode()
     *
     * @example & > &amp; | " > &quot; | ' > &apos; | > в &lt; | < в &gt;
     *
     * @param array|bool|float|int|string|null $data
     * @param int $flags битовая маска из флагов определяющая режим обработки
     * @param string $encoding кодировка
     * @param bool $doubleEncode при выключении не будет преобразовывать существующие HTML-сущности. При включении приведет к преобразованию &amp; > &amp;amp;
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     */
    public static function getHtmlSpecialCharsEncode(
        array|bool|float|int|string|null $data,
        int $flags = ENT_COMPAT | ENT_HTML5,
        string $encoding = 'UTF-8',
        bool $doubleEncode = false,
        bool $recursive = false
    ): array|string {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getHtmlEntityEncode($item, $flags, $encoding, $doubleEncode, $recursive);

                    } else {
                        $item = is_string($item) ? $item : VarStr::getMake($item);
                        $return[$key] = VarStr::getHtmlSpecialCharsEncode($item, $flags, $encoding, $doubleEncode);
                    }
                }
            }
        } else {
            $data = is_string($data) ? $data : VarStr::getMake($data);
            $return = VarStr::getHtmlSpecialCharsEncode($data, $flags, $encoding, $doubleEncode);
        }

        return $return;
    }

    /**
     * Удаляет пробелы из начала и конца строки (или другие символы при передаче их вторым параметром)
     *
     * @param array|string $removeChar
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     */
    public function trim(array|string $removeChar = " \t\n\r\0\x0B", bool $recursive = false): static
    {
        $this->data = static::getTrim($this->data, $removeChar, $recursive);

        return $this;
    }

    /**
     * Удаляет пробелы из начала и конца строки (или другие символы при передаче их вторым параметром)
     *
     * @param array|bool|float|int|string|null $data
     * @param array|string $removeChar список символов для удаления
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     */
    public static function getTrim(
        array|bool|float|int|string|null $data,
        array|string $removeChar = " \t\n\r\0\x0B",
        bool $recursive = false
    ): array|string {
        if (is_array($data) && is_array($return = [])) {
            reset($data);

            foreach ($data as $key => $item) {
                if ($recursive && is_array($item)) {
                    $return[$key] = static::getTrim($item, $removeChar, $recursive);

                } else {
                    $item = is_string($item) ? $item : VarStr::getMake($item);
                    $return[$key] = VarStr::trim($item, $removeChar);
                }
            }

        } else {
            $data = is_string($data) ? $data : VarStr::getMake($data);
            $return = VarStr::trim($data, $removeChar);
        }

        return $return;
    }

    /**
     * Удаление указанных символов из значений
     *
     * @param array|string $removeChar
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     */
    public function removeSymbol(array|string $removeChar = ["\n", "\r", "\t"], bool $recursive = false): static
    {
        $this->data = static::getRemoveSymbol($this->data, $removeChar, $recursive);

        return $this;
    }

    /**
     * Удаление указанных символов из значений
     *
     * @param array|bool|float|int|string|null $data
     * @param array|string $removeChar
     * @param bool $recursive - флаг для обхода потомков
     * @return array|string
     */
    public static function getRemoveSymbol(
        array|bool|float|int|string|null $data,
        array|string $removeChar = ["\n", "\r", "\t"],
        bool $recursive = false
    ): array|string {
        $removeChar = is_array($removeChar) ? $removeChar : [VarStr::getMake($removeChar)];

        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getRemoveSymbol($item, $removeChar, $recursive);

                    } else {
                        $item = is_string($item) ? $item : VarStr::getMake($item);
                        $return[$key] = VarStr::getRemoveSymbol($item, $removeChar);
                    }
                }
            }

        } else {
            $data = is_string($data) ? $data : VarStr::getMake($data);
            $return = VarStr::getRemoveSymbol($data, $removeChar);
        }

        return $return;
    }

    /**
     * Обрезает строку до 250 символов (без всяких условий)
     *
     * @param int $length
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     */
    public function crop(int $length = 250, bool $recursive = false): static
    {
        $this->data = static::getCrop($this->data, $length, $recursive);

        return $this;
    }

    /**
     * Обрезает строку до указанных символов
     *
     * @param array|bool|float|int|string|null $data
     * @param int $length
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     */
    public static function getCrop(
        array|bool|float|int|string|null $data,
        int $length = 250,
        bool $recursive = false
    ): array|string {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getCrop($item, $length, $recursive);

                    } else {
                        $item = is_string($item) ? $item : VarStr::getMake($item);
                        $return[$key] = VarStr::crop($item, $length);
                    }
                }
            }

        } else {
            $data = is_string($data) ? $data : VarStr::getMake($data);
            $return = VarStr::crop(VarStr::getMake($data), $length);
        }

        return $return;
    }

    /**
     * Сокращает текст по параметрам
     *
     * @param int $length
     * @param string $end
     * @param bool $transform преобразование символов
     * @param bool $smart флаг включающий умную систему резки с учётом целостности слов
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     */
    public function reduce(
        int $length = 250,
        string $end = '',
        bool $transform = true,
        bool $smart = true,
        bool $recursive = false
    ): static {
        $this->data = static::getReduce($this->data, $length, $end, $transform, $smart, $recursive);

        return $this;
    }

    /**
     * Сокращает текст по параметрам
     *
     * @param array|bool|float|int|string|null $data
     * @param int $length
     * @param string $end
     * @param bool $transform преобразование символов
     * @param bool $smart флаг включающий умную систему резки с учётом целостности слов
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     */
    public static function getReduce(
        array|bool|float|int|string|null $data,
        int $length = 250,
        string $end = '',
        bool $transform = true,
        bool $smart = true,
        bool $recursive = false
    ): array|string {
        if (is_array($data) && is_array($return = [])) {
            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
                        $return[$key] = static::getReduce($item, $length, $end, $transform, $smart, $recursive);

                    } else {
                        $item = is_string($item) ? $item : VarStr::getMake($item);
                        $return[$key] = VarStr::reduce($item, $length, $end, $transform, $smart);
                    }
                }
            }
        } else {
            $data = is_string($data) ? $data : VarStr::getMake($data);
            $return = VarStr::reduce($data, $length, $end, $transform, $smart);
        }

        return $return;
    }

    /**
     * Проверка строки или массива строк в которых содержатся списки цифр
     *
     * @param string $delimiter разделитель для строк
     * @param int $default значение по умолчанию
     * @param string $unique флаг проверки уникального значения. Если указали `single`, будет проверка в текущем ряду
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     * @throws Exception
     */
    public function options(
        string $delimiter = ',',
        int $default = 0,
        string $unique = 'single',
        bool $recursive = false
    ): static {
        $this->data = static::getOptions($this->data, $default, $delimiter, $unique, $recursive);

        return $this;
    }

    /**
     * Проверка строки или массива строк в которых содержатся списки цифр
     *
     * @param array|bool|float|int|string|null $data
     * @param int $default значение по умолчанию
     * @param string $delimiter разделитель для строк
     * @param string $unique флаг проверки уникального значения, в текущем ряду идентификаторов после использования $delimiter
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     * @throws Exception
     */
    public static function getOptions(
        array|bool|float|int|string|null $data,
        int $default = 0,
        string $delimiter = ',',
        string $unique = 'single',
        bool $recursive = false,
    ): array|string {
        if (VarInt::getMakePositiveInteger($default, 0) !== $default) {
            // В рамках концепции Options допускаются только положительные цифры
            throw new Exception("The default value must be a positive number");
        }

        if (is_array($data) && is_array($return = [])) {
            reset($data);

            foreach ($data as $key => $item) {
                if ($recursive && is_array($item)) {
                    $return[$key] = static::getOptions($item, $default, $delimiter, $unique, $recursive);

                } else {
                    $items = trim(is_string($item) ? $item : VarStr::getMake($item));
                    $items = VarStr::explode($delimiter, $items, []);
                    $items = VarArray::getRemove(static::getMinInteger($items, 0), ['', null]);

                    if (count($items) > 0) {
                        // Проверка на уникальность в текущем списке
                        $items = $unique === 'single' ? array_unique($items) : $items;
                        $return[$key] = join($delimiter, $items);
                    } else {
                        $return[$key] = (string)$default;
                    }
                }
            }

        } else {
            if (is_numeric($data)) {
                $return = (string)VarInt::getMakePositiveInteger($data, $default);

            } elseif (gettype($data) === 'string') {
                $return = static::getOptions([$data], $default, $delimiter, $unique, false);
                $return = VarArray::getFirst($return);

            } else {
                $return = (string)$default;
            }
        }

        return $return;
    }

    /**
     * Проверка строки или массива строк в которых содержатся списки идентификаторов
     *
     * @param string $delimiter разделитель строки
     * @param string $unique флаг проверки уникального значения. Если указали `single`, будет проверка в текущем ряду
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     * @throws Exception
     */
    public function ids(
        string $delimiter = ',',
        string $unique = 'single',
        bool $recursive = false
    ): static {
        $this->data = static::getIds($this->data, $delimiter, $unique, $recursive);

        return $this;
    }

    /**
     * Проверка строки или массива строк в которых содержатся списки идентификаторов
     *
     * @note метод рассчитан исключительно на строку или массивы строк в которых идентификаторы через символ разделителя!
     * @note длинна массива может отличаться от переданной после удаления недопустимых значений
     *
     * @param array|bool|float|int|string|null $data
     * @param string $delimiter разделитель строк(и) следует указывать если он отличается от запятой
     * @param string $unique флаг проверки уникального значения, в текущем ряду идентификаторов после использования $delimiter
     * @param bool $recursive флаг для обхода потомков
     * @return array|string
     * @throws Exception
     */
    public static function getIds(
        array|bool|float|int|string|null $data,
        string $delimiter = ',',
        string $unique = 'single',
        bool $recursive = false,
    ): array|string {
        if (is_array($data) && is_array($return = [])) {
            reset($data);

            foreach ($data as $key => $item) {
                if ($recursive && is_array($item)) {
                    $return[$key] = static::getIds($item, $delimiter, $unique, $recursive);

                } else {
                    $str = trim(is_string($item) ? $item : VarStr::getMake($item));
                    $ids = VarStr::explode($delimiter, $str, []);
                    $ids = static::getMinInteger($ids, 0);
                    $ids = VarArray::getRemove($ids, [0]);

                    if (count($ids) > 0) {
                        // Проверка на уникальность в текущем списке
                        $ids = $unique === 'single' ? array_unique($ids) : $ids;
                        $return[$key] = join($delimiter, $ids);
                    }
                }
            }

        } else {
            if (is_numeric($data)) {
                $return = $data > 0 ? VarStr::getMake($data) : '';

            } elseif (gettype($data) === 'string') {
                $list = static::getIds([$data], $delimiter, $unique, false);
                $return = VarArray::getFirst($list);
                $return = is_null($return) ? "" : $return;
            } else {
                $return = '';
            }
        }

        return $return;
    }

    /**
     * Проверка строку или массива строк в которых содержатся теги с указанным разделителем
     *
     * @param string $delimiter разделитель строки
     * @param string $unique флаг для проверки значений на уникальное повторение
     * @param bool $recursive флаг для обхода потомков
     * @return $this
     * @throws Exception
     */
    public function tags(string $delimiter = ',', string $unique = 'single', bool $recursive = false): static
    {
        $default = $this->getDefault();

        // Проверка типа только для строк, чисел, float, поскольку у нас допускается когда тип данных указан как массив, а default значение integer
        // К примеру get('status_id', -1, 'array')->getInteger('filter');
        if ($this->dataType !== 'array') {
            if (! (is_null($default) || is_numeric($default) || is_string($default))) {
                throw new Exception("Default values are not an string");
            }
        }

        $this->data = static::getTags($this->data, (string)$default, $delimiter, $unique, $recursive);

        return $this;
    }

    /**
     * Проверка строку или массива строк в которых содержатся теги с указанным разделителем
     *
     * @param array|bool|float|int|string|null $data
     * @param string $default значение по умолчанию
     * @param string $delimiter разделитель строки
     * @param string $unique флаг для проверки значений на уникальное повторение (в рамках одной строки)
     * @param bool $recursive флаг для обхода потомков
     * @param array $tmp
     * @return array|string
     */
    public static function getTags(
        array|bool|float|int|string|null $data,
        string $default,
        string $delimiter = ',',
        string $unique = 'single',
        bool $recursive = false,
        array &$tmp = []
    ): array|string {
        if (is_array($data) && is_array($return = [])) {

            if (count($data) > 0) {
                reset($data);

                foreach ($data as $key => $item) {
                    if ($recursive && is_array($item)) {
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
            $data = is_string($data) ? $data : VarStr::getMake($data);
            $data = VarStr::explode(",", $data, ['']);

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

    /**
     * Проверка строки или массива строк в которых содержатся списки чисел начиная от -1, 0, 1, 2, 3...
     *
     * @param string $delimiter разделитель строки
     * @param string $unique флаг проверки уникального значения. Если указали `single`, будет проверка в текущем ряду
     * @param bool $recursive флаг для обхода потомков
     * @param bool $saveIndex флаг сохраняющий в переданном массиве количество значений да-же если там удалены сами значения
     * @return $this
     * @throws Exception
     */
    public function filter(
        string $delimiter = ',',
        string $unique = 'single',
        bool $recursive = false,
        bool $saveIndex = false
    ): static {
        $this->data = static::getFilter($this->data, $delimiter, $unique, $recursive, $saveIndex);

        return $this;
    }

    /**
     * Проверка строки или массива строк в которых содержатся списки чисел начиная от -1, 0, 1, 2, 3...
     *
     * @note метод рассчитан исключительно на строку или массивы строк в которых идентификаторы через символ разделителя!
     * @note длинна массива может отличаться от переданной после удаления недопустимых значений
     * @note проверка значений переданного массива с флагом $removeEmptyIndex сохранит порядок и количество ключей, а значения будут пустыми!
     *
     * @param array|bool|float|int|string|null $data
     * @param string $delimiter разделитель строк(и) следует указывать если он отличается от запятой
     * @param string $unique флаг проверки уникального значения, в текущем ряду идентификаторов после использования $delimiter
     * @param bool $recursive флаг для обхода потомков
     * @param bool $saveIndex флаг сохраняющий в переданном массиве количество значений да-же если там удалены сами значения
     * @return array|string
     * @throws Exception
     */
    public static function getFilter(
        array|bool|float|int|string|null $data,
        string $delimiter = ',',
        string $unique = 'single',
        bool $recursive = false,
        bool $saveIndex = false
    ): array|string {
        if (is_array($data) && is_array($return = [])) {
            reset($data);

            foreach ($data as $key => $item) {
                if ($recursive && is_array($item)) {
                    $return[$key] = static::getFilter($item, $delimiter, $unique, $recursive);

                } else {
                    $str = trim(is_string($item) ? $item : VarStr::getMake($item));
                    $ids = VarStr::explode($delimiter, $str, []);

                    foreach ($ids as $index => $value) {
                        $value = (int)$value;

                        if ($value >= -1) {
                            $ids[$index] = $value;
                            continue;
                        }

                        unset($ids[$index]);
                    }

                    if (count($ids) > 0) {
                        // Проверка на уникальность в текущем списке
                        $ids = $unique === 'single' ? array_unique($ids) : $ids;
                        $return[$key] = join($delimiter, $ids);
                    } elseif ($saveIndex) {
                        $return[$key] = "";
                    }
                }
            }

        } else {
            if (is_numeric($data)) {
                $return = (int)$data >= -1 ? VarStr::getMake($data) : '';

            } elseif (gettype($data) === 'string') {
                $list = static::getFilter([$data], $delimiter, $unique, false);
                $return = VarArray::getFirst($list);
                $return = is_null($return) ? "" : $return;
            } else {
                $return = '';
            }
        }

        return $return;
    }

    /**
     * Удаление из строки или массива строк значения ноль "0"
     *
     * @param string $delimiter разделитель строки
     * @param bool $recursive флаг для обхода потомков
     * @param bool $saveIndex флаг сохраняющий в переданном массиве количество значений да-же если там удалены сами значения
     * @return $this
     * @throws Exception
     */
    public function removeZero(
        string $delimiter = ',',
        bool $recursive = false,
        bool $saveIndex = false
    ): static {
        $this->data = static::getFilter($this->data, $delimiter, $recursive, $saveIndex);

        return $this;
    }

    /**
     * Проверка строки или массива строк в которых содержатся списки чисел начиная от -1, 0, 1, 2, 3...
     *
     * @note метод рассчитан исключительно на строку или массивы строк в которых идентификаторы через символ разделителя!
     * @note длинна массива может отличаться от переданной после удаления недопустимых значений
     *
     * @param array|bool|float|int|string|null $data
     * @param string $delimiter разделитель строк(и) следует указывать если он отличается от запятой
     * @param bool $recursive флаг для обхода потомков
     * @param bool $saveIndex флаг сохраняющий в переданном массиве количество значений да-же если там удалены сами значения
     * @return array|string
     * @throws Exception
     */
    public static function getRemoveZero(
        array|bool|float|int|string|null $data,
        string $delimiter = ',',
        bool $recursive = false,
        bool $saveIndex = false
    ): array|string {
        if (is_array($data) && is_array($return = [])) {
            reset($data);

            foreach ($data as $key => $item) {
                if ($recursive && is_array($item)) {
                    $return[$key] = static::getRemoveZero($item, $delimiter, $recursive);

                } else {
                    $str = trim(is_string($item) ? $item : VarStr::getMake($item));
                    $ids = VarStr::explode($delimiter, $str, []);
                    $ids = array_map('intval', $ids);

                    foreach ($ids as $index => $value) {
                        if ($value === 0) {
                            unset($ids[$index]);
                        }
                    }

                    if (count($ids) > 0) {
                        $return[$key] = join($delimiter, $ids);
                    } elseif ($saveIndex) {
                        $return[$key] = "";
                    }
                }
            }

        } else {
            if (is_numeric($data)) {
                $return = $data != 0 ? VarStr::getMake($data) : '';

            } elseif (gettype($data) === 'string') {
                $list = static::getRemoveZero([$data], $delimiter, false);
                $return = VarArray::getFirst($list);
                $return = is_null($return) ? "" : $return;
            } else {
                $return = '';
            }
        }

        return $return;
    }
}
