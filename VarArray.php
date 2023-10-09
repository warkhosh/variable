<?php

namespace Warkhosh\Variable;

use Warkhosh\Variable\Helper\VarHelper;
use Closure;

/**
 * Class Arr
 * Методы для работы с массивами
 *
 * @note    : некоторые методы взяты из https://github.com/rappasoft/laravel-helpers/blob/master/src/helpers.php
 * @note    : некоторые методы взяты из https://github.com/illuminate/support/blob/master/helpers.php
 */
class VarArray
{
    /**
     * Преобразование переданного значения в массив.
     *
     * @param mixed  $data
     * @param string $delimiter
     * @return array
     */
    static public function getMakeArray($data = [], $delimiter = null)
    {
        if (gettype($data) === 'array') {
            return $data;
        }

        if (is_string($delimiter) && mb_strlen($delimiter) > 0) {
            $data = in_array(gettype($data), ['string', 'integer', 'double']) ? $data : '';

            return explode($delimiter, $data);
        }

        return (array)$data;
    }

    /**
     * Преобразование переданного значения в массив.
     *
     * @param mixed  $data
     * @param string $delimiter
     * @return void
     */
    static public function makeArray(&$data = [], $delimiter = null)
    {
        $data = static::getMakeArray($data, $delimiter);
    }

    /**
     * Устанавливает значение в элемент массива, используя для этого "точечную" нотацию.
     *
     * @note если ключ не задан или NULL, весь массив будет заменен.
     *
     * @param array  $inArray
     * @param string $key
     * @param mixed  $value
     * @return void
     */
    public static function set($key, $value, &$inArray)
    {
        if (is_null($key)) {
            $inArray = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // Если ключ не существует на этой глубине, мы просто создадим пустой массив для хранения следующего значения,
            // что позволит нам создать массивы для хранения окончательных значений на правильной глубине.
            // Так мы продолжим идти дальше по массиву.
            if (! isset($inArray[$key]) || ! is_array($inArray[$key])) {
                $inArray[$key] = [];
            }

            $inArray = &$inArray[$key];
        }

        $inArray[array_shift($keys)] = $value;
    }

    /**
     * Добавляет элемент в массив
     *
     * @note используя "точечную" нотацию
     *
     * @param string $key
     * @param mixed  $value
     * @param array  $inArray
     * @return array
     */
    public static function getAdded($key, $value, $inArray)
    {
        static::set($key, $value, $inArray);

        return $inArray;
    }

    /**
     * Добавляет элемент в массив
     *
     * @note используя "точечную" нотацию
     *
     * @param string $key
     * @param mixed  $value
     * @param array  $inArray
     * @return array
     */
    public static function apply($key, $value, &$inArray)
    {
        static::set($key, $value, $inArray);

        return $inArray;
    }

    /**
     * Добавляет значение в массив если он не существует
     *
     * @note используя "точечную" нотацию
     *
     * @param string $key
     * @param mixed  $value
     * @param array  $inArray
     * @return array
     */
    public static function add($key, $value, $inArray)
    {
        if (is_null(static::get($key, $inArray))) {
            static::set($key, $value, $inArray);
        }

        return $inArray;
    }

    /**
     * Свернуть массив массивов в один массив.
     *
     * @param array $array
     * @return array
     */
    public static function collapse(array $array)
    {
        $results = [];

        foreach ($array as $values) {
            if (! is_array($values)) {
                continue;
            }

            $results = array_merge($results, $values);
        }

        return $results;
    }

    /**
     * Добавить элемент в начало массива.
     *
     * @param array $array
     * @param mixed $value
     * @param mixed $key
     * @return array
     */
    public static function getPrepend($array, $value, $key = null)
    {
        if (is_null($key)) {
            array_unshift($array, $value);
        } else {
            $array = [$key => $value] + $array;
        }

        return $array;
    }

    /**
     * Получить значение из массива по ключу и удаление этого значения.
     *
     * @param string $key
     * @param array  $array
     * @param mixed  $default
     * @return mixed
     */
    public static function pull($key, &$array, $default = null)
    {
        $value = static::get($key, $array, $default);
        static::except($key, $array);

        return $value;
    }

    /**
     * Извлеките массив значений из массива.
     *
     * @param array             $array
     * @param string|array      $value
     * @param string|array|null $key
     * @return array
     */
    public static function getPluck($array, $value, $key = null)
    {
        $results = [];

        [$value, $key] = static::explodePluckParameters($value, $key);

        foreach ($array as $item) {
            $itemValue = static::getPluckData($item, $value);

            // Если ключ "null", мы просто добавим значение в массив и продолжим цикл.
            // В противном случае мы будем использовать массив, используя значение ключа, полученного нами от разработчика.
            // Затем мы вернем форму окончательного массива.
            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                $itemKey = static::getPluckData($item, $key);

                $results[$itemKey] = $itemValue;
            }
        }

        return $results;
    }

    /**
     * Получить элемент из массива или объекта с использованием нотации "точка" для Pluck метода.
     * Get an item from an array or object using "dot" notation.
     *
     * @param mixed        $target
     * @param string|array $key
     * @param mixed        $default
     * @return mixed
     */
    public static function getPluckData($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $default;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        foreach ($key as $segment) {
            if (is_array($target)) {
                if (! array_key_exists($segment, $target)) {
                    return VarHelper::value($default);
                }

                $target = $target[$segment];
            } elseif ($target instanceof \ArrayAccess) {
                if (! isset($target[$segment])) {
                    return VarHelper::value($default);
                }

                $target = $target[$segment];
            } elseif (is_object($target)) {
                if (! isset($target->{$segment})) {
                    return VarHelper::value($default);
                }

                $target = $target->{$segment};
            } else {
                return VarHelper::value($default);
            }
        }

        return $target;
    }

    /**
     * Взорвите аргументы "value" и "key", переданные static::getPluck().
     *
     * @param string|array      $value
     * @param string|array|null $key
     * @return array
     */
    protected static function explodePluckParameters($value, $key)
    {
        $value = is_array($value) ? $value : (is_string($value) ? explode('.', $value) : (array)$value);

        $key = is_null($key) || is_array($key) ? $key : (is_string($key) ? explode('.', $key) : null);

        return [$value, $key];
    }

    /**
     * Получить элемент из массива с использованием нотации "точка".
     *
     * @param null|string|int|array $key
     * @param array                 $array
     * @param mixed                 $default
     * @return mixed
     */
    public static function get($key, array $array = [], $default = null)
    {
        if (is_null($key)) {
            return $default;
        }

        if (is_string($key) && VarStr::find(".", $key) === false) {
            if (is_array($array) && array_key_exists($key, $array)) {
                return $array[$key];
            }

            return $default;
        }

        $keys = is_array($key) ? $key : explode('.', (string)$key);

        foreach ($keys as $segment) {
            if (! is_array($array) || ! array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Получение числа из массива с использованием нотации "точка".
     *
     * @note используйте если вам по логике всегда нужно число!
     *
     * @param       $key
     * @param array $array
     * @param null  $default
     * @return int
     */
    public static function getGreaterZero($key, $array = [], $default = 1)
    {
        $int = (int)static::get($key, $array, $default);

        return ($int > 0) ? $int : intval($default);
    }

    /**
     * Проверяет, существует ли данный ключ в предоставленном массиве.
     *
     * @param string|int         $key
     * @param \ArrayAccess|array $array
     * @return bool
     */
    public static function exists($key, $array)
    {
        if ($array instanceof \ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    /**
     * Проверить, присутствует ли элемент в массиве с помощью "точки".
     *
     * @param string $key
     * @param array  $array
     * @param bool   $dot - флаг разбития строки символом точки для поиска подзначений
     * @return bool
     */
    public static function has($key = null, $array = [], $dot = true)
    {
        if (empty($array) || ! (is_string($key) || is_numeric($key) || is_float($key))) {
            return false;
        }

        if (array_key_exists($key, $array)) {
            return true;
        }

        if ($dot) {
            foreach (explode('.', $key) as $segment) {
                if (! is_array($array) || ! array_key_exists($segment, $array)) {
                    return false;
                }

                $array = $array[$segment];
            }
        }

        return true;
    }

    /**
     * Проверка наличие списка ключей в массиве ( возможна проверка вложеного массива с помощью "точки" ).
     *
     * @param array $key
     * @param array $array
     * @return bool
     */
    public static function hasKeys($key, array $array = [])
    {
        $array = (array)$array;
        $key = (array)$key;

        if (count($key) === 0 && count($array) === 0) {
            return true;

        } elseif (count($key) > 0 && count($array) > 0) {
            foreach ($key as $check) {
                if (static::has($check, $array) !== true) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Возвращает ключи из списка где значение равно указанному
     *
     * @param       $value
     * @param array $array
     * @return array
     */
    public static function getSearchKeys($value, array $array = [])
    {
        $result = [];

        foreach ($array as $key => $str) {
            if ($value == $str) {
                $result[] = $key;
            }
        }

        return $result;
    }

    /**
     * Проверка наличие списка значений в плоском массиве
     *
     * @param string|array $values
     * @param array        $array
     * @return bool
     */
    public static function hasValues($values, $array = [])
    {
        $values = (array)$values;
        $array = (array)$array;

        if (count($values) === 0 && count($array) === 0) {
            return true;

        } elseif (count($values) > 0 && count($array) > 0) {
            foreach ($values as $check) {
                if (! in_array($check, $array)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Сгладьте многомерный массив на один уровень.
     *
     * @param array $array
     * @param int   $depth
     * @return array
     */
    public static function getFlatten($array = [], $depth = INF)
    {
        if (! is_array($array)) {
            return [];
        }

        return array_reduce($array, function($result, $item) use ($depth) {
            if (! is_array($item)) {
                return array_merge($result, [$item]);

            } elseif ($depth === 1) {
                return array_merge($result, array_values($item));

            } else {
                return array_merge($result, static::getFlatten($item, $depth - 1));
            }
        }, []);
    }

    /**
     * Возвращает массив где все вложенные элементы становятся плоски с последовательными ключами в ключах
     *
     * @param array  $array
     * @param string $separator
     * @param string $prefix
     * @return array
     */
    public static function getSimplify($array = [], $separator = "_", $prefix = "")
    {
        $return = [];
        $recFunction = function($fun, $separator, $prefix, $array = [], array $keys = [], &$return = []) {
            if (is_array($array)) {
                foreach ($array as $key => $value) {
                    $keyList = $keys;
                    $keyList[] = $key;
                    $fun($fun, $separator, $prefix, $value, $keyList, $return);
                }

            } else {
                if (! empty($prefix)) {
                    $keys = array_merge([$prefix], $keys);
                }

                $return[join($separator, $keys)] = $array;
            }
        };

        if (is_array($array)) {
            $recFunction($recFunction, $separator, $prefix, $array, [], $return);
        }

        return $return;
    }

    /**
     * Сглаживание многомерного массива в один уровень
     *
     * @param array $array
     * @param int   $depth
     * @return void
     */
    public static function flatten(&$array = [], $depth = INF)
    {
        $array = static::getFlatten($array, $depth);
    }

    /**
     * Определяет, является ли массив ассоциативным.
     *
     * Массив "ассоциативный", если он не имеет последовательных цифровых клавиш, начиная с нуля.
     *
     * @param array $array
     * @return bool
     */
    public static function isAssoc(array $array)
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    /**
     * Sort the array using the given callback.
     *
     * @param array    $array
     * @param callable $callback
     * @return array
     *
     * public static function sort($array, callable $callback)
     * {
     * return Collection::make($array)->sortBy($callback)->all();
     * }*/

    /**
     * Рекурсивно отсортировать массив по ключам и значениям.
     *
     * @param array $array
     * @return array
     */
    public static function getSortRecursive($array)
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = static::getSortRecursive($value);
            }
        }

        if (static::isAssoc($array)) {
            ksort($array);
        } else {
            sort($array);
        }

        return $array;
    }

    /**
     * Сортировка массива по возрастанию
     *
     * @param array $array
     * @param string|Closure $field
     * @param int $options
     * @param bool $descending
     * @return array
     */
    public static function sortBy(array $array, $field, int $options = SORT_REGULAR, bool $descending = false)
    {
        if (! $field instanceof Closure) {
            $field = function ($item) use ($field) {
                return static::get($field, $item);
            };
        }

        foreach ($array as $key => $value) {
            $results[$key] = $field($value);
        }

        $descending ? arsort($results, $options) : asort($results, $options);

        foreach (array_keys($results) as $key) {
            $results[$key] = $array[$key];
        }

        return $results;
    }

    /**
     * Сортировка массива по убыванию
     *
     * @param array $array
     * @param string|Closure $field
     * @param int $options
     * @return array
     */
    public static function sortByDesc(array $array, $field, int $options = SORT_REGULAR)
    {
        return static::sortBy($array, $field, $options, true);
    }

    /**
     * Фильтруйте массив, используя данный обратный вызов.
     *
     * @param array    $array
     * @param callable $callback
     * @return array
     */
    public static function getWhere($array, callable $callback)
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Если указаное значение не является массивом, оберните в массив.
     *
     * @param mixed $value
     * @return array
     */
    public static function getWrap($value = null)
    {
        if (is_null($value)) {
            return [];
        }

        return ! is_array($value) ? [$value] : $value;
    }

    /**
     * @param string     $delimiter
     * @param string     $string
     * @param null|array $delete
     * @return array
     */
    public static function explode($delimiter, $string, $delete = ['', 0, null])
    {
        return VarArray::getRemove(explode($delimiter, (string)$string), VarHelper::getArrayWrap($delete, false));
    }

    /**
     * Удаляет в массиве один или несколько ключей из переданых значений
     *
     * @note в удаляемых ключах допускается точка для вложенного действия
     *
     * @param array | string $keys  - ключи которые надо исключить
     * @param array          $array - массив в котором убираем значения по ключам
     * @return void
     */
    public static function itemsExcept($keys, &$array)
    {
        if (gettype($array) === 'array' && count($array) > 0) {
            foreach ($array as &$rows) {
                static::forget($keys, $rows);
            }

            reset($array);

        } else {
            $array = [];
        }
    }

    /**
     * Удаляет в массиве один или несколько ключей из переданых значений
     *
     * @note в удаляемых ключах допускается точка для вложенного действия
     *
     * @param array | string $keys  - ключи которые надо исключить
     * @param array          $array - массив в котором убираем значения по ключам
     * @return array
     */
    public static function getItemsExcept($keys = [], $array = [])
    {
        if (gettype($array) === 'array' && count($array) > 0) {
            foreach ($array as &$rows) {
                static::forget($keys, $rows);
            }

            reset($array);

        } else {
            $array = [];
        }

        return $array;
    }

    /**
     * Удаляет в массиве один или несколько ключей из переданых значений
     *
     * @note в удаляемых ключах допускается точка для вложенного действия
     *
     * @param array | string $keys  - ключи которые надо исключить
     * @param array          $array - массив в котором убираем значения по ключам
     * @return void
     */
    static public function except($keys, &$array)
    {
        static::forget($keys, $array);
    }

    /**
     * Возвращает все элементы, кроме тех, чьи ключи указаны в передаваемом массиве
     *
     * @note в удаляемых ключах допускается точка для вложенного действия
     *
     * @param array | string $keys  - ключи которые надо исключить
     * @param array          $array - массив в котором убираем значения по ключам
     * @return array
     */
    static public function getExcept($keys = [], $array = [])
    {
        static::forget($keys, $array);

        return $array;
    }

    /**
     * Удаляет в массиве один или несколько ключей из переданых значений.
     *
     * @note в удаляемых ключах допускается точка для вложенного действия
     * @note улучшеный вариант without() но взят из ларавеля и нуно переписать!
     *
     * @param array | string $keys  - ключи которые надо исключить
     * @param array          $array - массив в котором убираем значения по ключам
     * @return void
     */
    public static function forget($keys, &$array)
    {
        $original = &$array;
        $keys = (array)$keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            // если точный ключ существует на верхнем уровне, удалите его
            if (static::exists($key, $array)) {
                unset($array[$key]);

                continue;
            }

            $parts = explode('.', $key);

            // очищать перед каждым проходом
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }

    /**
     * Возвращает первый элемента массива, прошедшего заданный тест истинности.
     *
     * @note Вы также можете вызвать метод без аргументов, чтобы получить первый элемент в списке.
     * @param array    $array
     * @param callable $callback
     * @param mixed    $default
     * @return mixed
     */
    public static function getFirst($array = [], callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            if (empty($array)) {
                return VarHelper::value($default);
            }

            foreach ($array as $item) {
                return $item;
            }
        }

        foreach ($array as $key => $value) {
            if (call_user_func($callback, $value, $key)) {
                return $value;
            }
        }

        return VarHelper::value($default);
    }

    /**
     * Возвращает второй элемент массива, прошедшего заданный тест истинности.
     *
     * @note Вы также можете вызвать метод без аргументов, чтобы получить второй элемент в списке.
     * @param array    $array
     * @param callable $callback
     * @param mixed    $default
     * @return mixed
     */
    public static function getSecond($array = [], callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            if (empty($array)) {
                return VarHelper::value($default);
            }

            return empty($result = array_slice($array, 1, 1)) ? VarHelper::value($default) : current($result);
        }

        foreach ($array as $key => $value) {
            if (call_user_func($callback, $value, $key)) {
                return $value;
            }
        }

        return VarHelper::value($default);
    }

    /**
     * Возвращает последний элемент в массиве, прошедшего заданный тест истинности
     *
     * @note можно вызвать последний метод без аргументов, чтобы получить последний элемент в коллекции
     *
     * @param array    $array
     * @param callable $callback
     * @param mixed    $default
     * @return mixed
     */
    public static function getLast($array = [], callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? VarHelper::value($default) : end($array);
        }

        return static::getFirst(array_reverse($array), $callback, $default);
    }

    /**
     * Возвращает ключ последнего элемента в массиве
     *
     * @note если передали условие в $callback, будет последовательно его проверять с каждым элементом
     *
     * @param array    $array
     * @param callable $callback
     * @param mixed    $default
     * @return mixed
     */
    public static function getLastKey($array = [], callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            if (is_array($array) && count($array) > 0) {
                end($array);

                return key($array);
            }

            return $default;
        }

        $array = array_reverse($array);

        foreach ($array as $key => $value) {
            if (call_user_func($callback, $value, $key)) {
                return $key;
            }
        }

        return $default;
    }

    /**
     * Группирует элементы массива по заданному ключу
     *
     * @param string $key      - ключах допускается точка для вложенного действия
     * @param array  $arr      - Список который будет группироваться по указанному ключу
     * @param bool   $multiple - флаг группировки с подмножеством значений. Не работает при вложенных группировка с точкой!
     * @return array
     */
    public static function getGroupBy($key = null, $arr = [], $multiple = false)
    {
        $result = [];
        $keys = VarStr::explode(".", $key, ['']);
        $depth = count($keys);

        if (count($arr) > 0) {
            if ($depth > 1) {

                // Группируем значения по первому значению
                $result = self::getGroupBy($keys[0], $arr, false);

                $key2 = array_shift($keys); // Извлекаю ключ по которому была группировка

                // Прохожу по сгруппированному списку
                foreach ($result as $k1 => $row) {
                    $group2 = [];

                    // Внутри каждой группы перебираю основной список
                    foreach ($arr as $list) {
                        // Формирую новый список в котором в элементах есть ключ и значение это группы
                        if (key_exists($key2, $list) && $k1 == $list[$key2]) {
                            $group2[] = $list;
                        }
                    }

                    // Группирую список по вложенному ключу
                    $result[$k1] = static::getGroupBy($keys[0], $group2,
                        $multiple); // важно передать изначальный $multiple!
                }

                $key3 = array_shift($keys); // Извлекаю ключ по которому была группировка

                // Проверка наличие вложенной группировки по третьему значению
                if (count($keys) >= 1) {
                    // Прохожу по сгруппированному списку
                    foreach ($result as $k1 => $rows2) {
                        foreach ($rows2 as $k2 => $rows3) {
                            $group3 = [];

                            foreach ($arr as $list) {
                                if (key_exists($key2, $list) &&
                                    $k1 == $list[$key2] &&
                                    key_exists($key3, $list) &&
                                    $k2 == $list[$key3]) {
                                    $group3[] = $list;
                                }
                            }

                            // Группирую список по вложенному ключу
                            $result[$k1][$k2] = static::getGroupBy($keys[0], $group3,
                                $multiple); // важно передать изначальный $multiple!
                        }
                    }
                }

                $key4 = array_shift($keys); // Извлекаю ключ по которому была группировка

                // Проверка наличие вложенной группировки по четвертому значению
                if (count($keys) >= 1) {
                    // Прохожу по сгруппированному списку
                    foreach ($result as $k1 => $rows2) {
                        foreach ($rows2 as $k2 => $rows3) {
                            foreach ($rows3 as $k3 => $rows4) {
                                $group4 = [];

                                foreach ($arr as $list) {
                                    if (key_exists($key2, $list) &&
                                        $k1 == $list[$key2] &&
                                        key_exists($key3, $list) &&
                                        $k2 == $list[$key3] &&
                                        key_exists($key4, $list) &&
                                        $k3 == $list[$key4]) {
                                        $group4[] = $list;
                                    }
                                }

                                // Группирую список по вложенному ключу
                                $result[$k1][$k2][$k3] = static::getGroupBy($keys[0], $group4,
                                    $multiple); // важно передать изначальный $multiple!
                            }
                        }
                    }
                }

            } else {

                foreach ($arr as $row) {
                    if (isset($row[$key]) && array_key_exists($key, $row)) {
                        if ($multiple === true) {
                            $result[$row[$key]][] = $row;
                        } else {
                            $result[$row[$key]] = $row;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Группирует элементы массива по заданному ключу с записью только параметра
     *
     * @param string       $groupKey - ключ по которому группируются поля
     * @param array        $arr      - список который перебираем
     * @param string|array $with     - ключ который будет сохранен. Если не указать то будет сохранен ключ от группировки
     * @param bool         $multiple - флаг группировки с подмножеством значений
     * @return array
     */
    public static function getGroupWith($groupKey = null, $arr = [], $with = null, $multiple = false)
    {
        $result = [];

        if (count($arr) > 0) {
            @reset($arr);

            foreach ($arr as $row) {
                if (isset($row[$groupKey]) && array_key_exists($groupKey, $row)) {
                    if ($multiple === true) {
                        if (is_array($with)) {
                            if (count($tmp = VarArray::getOnly($with, $row))) {
                                $result[$row[$groupKey]][] = $tmp;
                            }
                        } else {
                            $result[$row[$groupKey]][] = array_key_exists($with, $row) ? $row[$with] : $row[$groupKey];
                        }
                    } else {
                        if (is_array($with)) {
                            if (count($tmp = VarArray::getOnly($with, $row))) {
                                $result[$row[$groupKey]] = $tmp;
                            }
                        } else {
                            $result[$row[$groupKey]] = array_key_exists($with, $row) ? $row[$with] : $row[$groupKey];
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Возвращает список дополненный определенным значением до указанной длины
     *
     * @note если элементов было больше указаного числа то они не удаляются из результата
     *
     * @param array   $list    - список
     * @param integer $length  - длина будущего списка
     * @param mixed   $default - значение для элементов которые будут созданы как недостающие
     * @return array
     */
    static public function getPadList(array $list, $length = 0, $default = null)
    {
        $list = array_pad($list, $length, $default);

        return array_values($list);
    }

    /**
     * Удаление переданых значений из массива
     *
     * @param array $arr
     * @param array $delete
     * @return array
     */
    public static function getRemove(array $arr, $delete = ['', 0, null])
    {
        return array_diff($arr, VarHelper::getArrayWrap($delete, false));
    }

    /**
     * Удаление переданых значений по ссылке из указаного массива
     *
     * @param array $arr
     * @param array $delete
     */
    public static function remove(&$arr, $delete = ['', 0, null])
    {
        $arr = array_diff($arr, VarHelper::getArrayWrap($delete, false));
    }

    /**
     * Возвращает из массива данные по указанным ключам
     *
     * @param array|string $keys
     * @param array        $arr
     * @param bool         $required - флаг обязательного наличия ключей при возврате равное указаному
     * @param null         $default  - значение используется есовместно с $required = TRUE
     * @return array|null
     */
    static public function getExtract($keys = null, $arr = [], $required = true, $default = null)
    {
        static::extract($keys, $arr, $required, $default);

        return $arr;
    }

    /**
     * Извлекает (по ссылке) из массива данные по указанным ключам
     *
     * @param array        $arr
     * @param array|string $keys
     * @param bool         $required - флаг обязательного наличия ключей при возврате равное указаному
     * @param null         $default  - значение используется есовместно с $required = TRUE
     * @return void
     */
    static public function extract($keys = null, &$arr = [], $required = true, $default = null)
    {
        // Сценарий плоского извлечения
        if (is_array($keys) && count($keys) > 0 && is_array($return = [])) {
            foreach ($keys as $key) {
                if (isset($arr[$key]) && array_key_exists($key, $arr)) {
                    $return[$key] = $arr[$key];
                    unset($arr[$key]);

                } elseif ($required) {
                    $return[$key] = $default;
                }
            }

            $arr = $return;

        } elseif (is_string($keys) && mb_strlen($keys) > 0) {
            $return = $default;
            $parts = explode('.', $keys);
            $last = $parts[count($parts) - 1];

            while (count($parts) >= 1) {
                $part = array_shift($parts);

                // переходим на уровень ниже
                if ($last != $part && isset($arr[$part]) && is_array($arr[$part])) {
                    $arr = &$arr[$part];

                } else {
                    // сюда попадают когда дошли до нужного уровня или массив не имеет больше потомков
                    $parts = [];

                    // смотрим наличие последнего ключа для проверки и удаление по нему его значений
                    if ($last == $part && isset($arr[$last]) && array_key_exists($last, $arr)) {
                        $return = $arr[$last];
                        unset($arr[$last]);

                    } elseif ($required) {
                        $return = $default;
                    }
                }
            }

            $arr = $return;

        } else {
            $arr = $default;
        }
    }

    /**
     * Извлекает в массиве один или несколько ключей из переданых значений
     *
     * @note в извлекаемых ключах допускается точка для вложенного действия
     *
     * @param array | string $keys  - ключи которые надо извлечь и оставить
     * @param array          $array - массив в котором производим извлечения
     * @return void
     */
    public static function itemsExtract($keys, &$array)
    {
        if (gettype($array) === 'array' && count($array) > 0) {
            foreach ($array as &$rows) {
                static::extract($keys, $rows);
            }

            reset($array);

        } else {
            $array = [];
        }
    }

    /**
     * Извлекает в массиве один или несколько ключей из переданых значений
     *
     * @note в извлекаемых ключах допускается точка для вложенного действия
     *
     * @param array | string $keys  - ключи которые надо исключить
     * @param array          $array - массив в котором убираем значения по ключам
     * @return array
     */
    public static function getItemsExtract($keys = [], $array = [])
    {
        if (gettype($array) === 'array' && count($array) > 0) {
            foreach ($array as &$rows) {
                static::extract($keys, $rows);
            }

            reset($array);

        } else {
            $array = [];
        }

        return $array;
    }

    /**
     * Альтернатива each
     *
     * @note: функция each() объявлена УСТАРЕВШЕЙ начиная с PHP 7.2.0 и ее использование крайне не рекомендовано
     *
     * @param array $arr
     * @return array|false
     */
    static public function each(array $arr)
    {
        while (($key = key($arr)) !== null) {
            next($arr); // сдвигаем указатель на одну позицию вперёд

            return [$key, $arr[$key]];
        }

        return false;
    }

    /**
     * Удаляет экранирование символов
     *
     * @param array $arr
     * @param bool  $recursive
     * @return array
     */
    static public function stripSlashes(array $arr = [], $recursive = false)
    {
        $return = [];

        if (is_array($arr) && count($arr) > 0) {
            reset($arr);

            foreach ($arr as $key => $item) {
                if ($recursive === true && is_array($item)) {
                    $return[$key] = static::stripslashes($item, $recursive);

                } else {
                    $return[$key] = stripslashes(! is_string($item) ? VarStr::getMakeString($item) : $item);
                }
            }
        }

        return $return;
    }

    /**
     * Удаляет пробелы из начала и конца строки (или другие символы при передачи их вторым параметром )
     *
     * @param array  $arr
     * @param string $removeChar - список символов для удаления
     * @param bool   $recursive  - флаг для обхода потомков
     * @return array
     */
    static public function trim(array $arr = [], $removeChar = " \t\n\r\0\x0B", $recursive = false)
    {
        $return = [];

        if (is_array($arr) && count($arr) > 0) {
            reset($arr);

            foreach ($arr as $key => $item) {
                if ($recursive === true && is_array($item)) {
                    $return[$key] = static::trim($item, $removeChar, $recursive);

                } else {
                    $return[$key] = VarStr::trim($item, $removeChar);
                }
            }
        }

        return $return;
    }

    /**
     * Замена повторяющегося символа
     *
     * @note нужно учитывать что списки должны совпадать по длине!
     *
     * @param array          $arr
     * @param string | array $char
     * @param string | array $replace
     * @param bool           $recursive - флаг для обхода потомков
     * @return array
     */
    static public function getRemovingDoubleChar(
        array $arr = [],
        $char = ' ',
        $replace = ' ',
        $recursive = false
    ) {
        $return = [];

        if (is_array($arr) && count($arr) > 0) {
            reset($arr);

            foreach ($arr as $key => $item) {
                if ($recursive === true && is_array($item)) {
                    $return[$key] = static::getRemovingDoubleChar($item, $char, $replace, $recursive);

                } else {
                    $return[$key] = VarStr::getRemovingDoubleChar($item, $char, $replace);
                }
            }
        }

        return $return;
    }

    /**
     * Преобразование значений в целое число с проверкой на минимальное значение
     *
     * @note: возможны отрицательные значения!
     *
     * @param array $data
     * @param int   $default
     * @param bool  $recursive
     * @return array|int
     */
    static function getMinInt(array $data, $default = 0, $recursive = false)
    {
        $default = VarInt::getMakeInteger($default);

        if (is_array($data) && count($data) > 0 && is_array($return = [])) {
            reset($data);

            foreach ($data as $key => $item) {
                if ($recursive === true && is_array($item)) {
                    $return[$key] = static::getMinInt($item, $default, $recursive);

                } else {
                    $item = VarInt::getMakeInteger($item, $default);
                    $return[$key] = $item >= $default ? $item : $default;
                }
            }

            return $return;
        }

        return [];
    }

    /**
     * Преобразование значений в целое число с проверкой на минимальное значение
     *
     * @note: возможны отрицательные значения!
     *
     * @param array $data
     * @param int   $default
     * @param bool  $recursive
     * @return void
     */
    static function minInt(&$data, $default = 0, $recursive = false)
    {
        $data = static::getMinInt($data, $default, $recursive);
    }

    /**
     * Преобразование значения в целое число с проверкой его на максимальное значение
     *
     * @note: возможны отрицательные значения!
     *
     * @param array $data
     * @param int   $max       - число предела
     * @param bool  $toDefault - флаг преобразования числа вышедего за пределы в default или max
     * @param int   $default   - значение по умолчанию
     * @param bool  $recursive
     * @return array|int
     */
    static function getMaxInt(array $data, $max = 0, $toDefault = true, $default = 0, $recursive = false)
    {
        $default = VarInt::getMakeInteger($default);

        if (is_array($data) && count($data) > 0 && is_array($return = [])) {
            reset($data);

            foreach ($data as $key => $item) {
                if ($recursive === true && is_array($item)) {
                    $return[$key] = static::getMaxInt($item, $max, $toDefault, $default, $recursive);

                } else {
                    $item = VarInt::getMakeInteger($item, $default);
                    $return[$key] = $item;

                    if ($item > $max) {
                        $return[$key] = $toDefault ? $default : $max;
                    }
                }
            }

            return $return;
        }

        return [];
    }

    /**
     * Преобразование значения в целое число с проверкой его на максимальное значение
     *
     * @note: возможны отрицательные значения!
     *
     * @param array $data
     * @param int   $max       - число предела
     * @param bool  $toDefault - флаг преобразования числа вышедего за пределы в default или max
     * @param int   $default   - значение по умолчанию
     * @param bool  $recursive
     * @return void
     */
    static function maxInt(&$data, $max = 0, $toDefault = true, $default = 0, $recursive = false)
    {
        $data = static::getMaxInt($data, $max, $toDefault, $default, $recursive);
    }

    /**
     * Оставить подмножество элементов из заданного массива.
     *
     * @param array | string $haystack - список с допустимых значений
     * @param array          $array    - список который фильтруем
     * @return array
     */
    public static function getOnly($haystack, array $array)
    {
        return array_intersect_key($array, array_flip((array)$haystack));
    }

    /**
     * Оставить подмножество элементов из заданного массива.
     *
     * @param array | string $haystack - список с допустимымых значений
     * @param array          $array    - список который фильтруем
     * @return void
     */
    static function only($haystack, array &$array)
    {
        $array = static::getOnly((array)$haystack, $array);
    }

    /**
     * Оставить указаное подмножество элементов в списках.
     *
     * @param array | string $haystack - список с допустимых значений
     * @param array          $array    - список который фильтруем
     * @return array
     */
    static function getItemsOnly($haystack, array $array)
    {
        if (count($array)) {
            $haystack = is_array($haystack) ? $haystack : (array)$haystack;

            foreach ($array as $key => $values) {
                $array[$key] = static::getOnly($haystack, $values);
            }
        }

        return $array;
    }

    /**
     * Оставить указаное подмножество элементов в списках.
     *
     * @param array | string $haystack - список с допустимых значений
     * @param array          $array    - список который фильтруем
     * @return void
     */
    static function itemsOnly($haystack, array &$array)
    {
        if (count($array)) {
            $haystack = is_array($haystack) ? $haystack : (array)$haystack;

            foreach ($array as $key => $values) {
                $array[$key] = static::getOnly($haystack, $values);
            }
        }
    }

    /**
     * Преобразует первый символ строки в верхний регистр
     *
     * @param array $data
     * @param bool  $recursive
     * @return array
     */
    static public function ucfirst(array $data, $recursive = false)
    {
        if (is_array($data) && count($data) > 0 && is_array($return = [])) {
            reset($data);

            foreach ($data as $key => $item) {
                if ($recursive === true && is_array($item)) {
                    $return[$key] = static::ucfirst($item, $recursive);

                } else {
                    $return[$key] = VarStr::ucfirst($item);
                }
            }

            return $return;
        }

        return [];
    }

    /**
     * Преобразует все символы в верхний регистр.
     *
     * @param array $arr
     * @param bool  $recursive - флаг для обхода потомков
     * @return array
     */
    static public function getUpper(array $arr = [], $recursive = false)
    {
        $return = [];

        if (is_array($arr) && count($arr) > 0) {
            reset($arr);

            foreach ($arr as $key => $item) {
                if ($recursive === true && is_array($item)) {
                    $return[$key] = static::getUpper($item, $recursive);

                } else {
                    $return[$key] = VarStr::getUpper($item);
                }
            }
        }

        return $return;
    }

    /**
     * Преобразует все символы в нижний регистр.
     *
     * @param array $arr
     * @param bool  $recursive - флаг для обхода потомков
     * @return array
     */
    static public function getLower(array $arr = [], $recursive = false)
    {
        $return = [];

        if (is_array($arr) && count($arr) > 0) {
            reset($arr);

            foreach ($arr as $key => $item) {
                if ($recursive === true && is_array($item)) {
                    $return[$key] = static::getLower($item, $recursive);

                } else {
                    $return[$key] = VarStr::getLower($item);
                }
            }
        }

        return $return;
    }

    /**
     * Получить один или несколько элементов случайным образом из массива.
     *
     * @param array   $arr    - список из которого выбираем
     * @param integer $amount - какое количество элементов вернуть
     * @param boolean $saveKey
     * @return array
     */
    static public function getRandomItems(array $arr = [], $amount = 1, $saveKey = true)
    {
        if ($amount > ($count = count($arr))) {
            $amount = $count;
        }

        if ($count > 0) {
            $keys = array_rand($arr, $amount);
            $keys = $amount > 1 ? $keys : [$keys];
            $result = [];

            foreach ($keys as $key) {
                if ($saveKey) {
                    $result[$key] = $arr[$key];
                } else {
                    $result[] = $arr[$key];
                }
            }

            return $result;
        }

        return $arr;
    }

    /**
     * Проверка значения на не пустой массив
     *
     * @param mixed $arr
     * @param int   $length
     * @return bool
     */
    static public function isNotEmpty($arr, $length = 0)
    {
        if (is_array($arr) && count($arr) > (int)$length) {
            return true;
        }

        return false;
    }
}
