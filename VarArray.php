<?php

namespace Warkhosh\Variable;

use ArrayAccess;
use Exception;
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
     * Преобразование переданного значения в массив
     *
     * @param mixed $data
     * @param string|null $delimiter
     * @return array
     */
    public static function getMakeArray(mixed $data = [], ?string $delimiter = null): array
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
     * Преобразование переданного значения в массив
     *
     * @param mixed $data
     * @param string|null $delimiter
     * @return void
     */
    public static function makeArray(mixed &$data = [], ?string $delimiter = null): void
    {
        $data = static::getMakeArray($data, $delimiter);
    }

    /**
     * Устанавливает значение в элемент массива, используя для этого "точечную" нотацию
     *
     * @note если ключ не задан или NULL, весь массив будет заменен.
     *
     * @param string|null $key
     * @param mixed $value
     * @param array $inArray
     * @return void
     */
    public static function set(?string $key, mixed $value, array &$inArray): void
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
     * @param mixed $value
     * @param array $inArray
     * @return array
     */
    public static function getAdded(string $key, mixed $value, array $inArray): array
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
     * @param mixed $value
     * @param array $inArray
     * @return array
     */
    public static function apply(string $key, mixed $value, array &$inArray): array
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
     * @param mixed $value
     * @param array $inArray
     * @return array
     */
    public static function add(string $key, mixed $value, array $inArray): array
    {
        if (is_null(static::get($key, $inArray))) {
            static::set($key, $value, $inArray);
        }

        return $inArray;
    }

    /**
     * Свернуть массив массивов в один массив
     *
     * @param array $array
     * @return array
     */
    public static function collapse(array $array): array
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
     * Добавить элемент в начало массива
     *
     * @param array $array
     * @param mixed $value
     * @param string|null $key
     * @return array
     */
    public static function getPrepend(array $array, mixed $value, ?string $key = null): array
    {
        if (is_null($key)) {
            array_unshift($array, $value);
        } else {
            $array = [$key => $value] + $array;
        }

        return $array;
    }

    /**
     * Получить значение из массива по ключу и удаление этого значения
     *
     * @param string $key
     * @param array $array
     * @param mixed $default
     * @return mixed
     */
    public static function pull(string $key, array &$array, mixed $default = null): mixed
    {
        $value = static::get($key, $array, $default);
        static::except($key, $array);

        return $value;
    }

    /**
     * Извлеките массив значений из массива
     *
     * @param array $array
     * @param array|string $value
     * @param array|string|null $key
     * @return array
     */
    public static function getPluck(array $array, array|string $value, array|string|null $key = null): array
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
     * Получить элемент из массива или объекта с использованием нотации "точка" для Pluck метода
     * Get an item from an array or object using "dot" notation.
     *
     * @param mixed $target
     * @param array|string|null $key
     * @param mixed $default
     * @return mixed
     */
    public static function getPluckData(mixed $target, array|string|null $key, mixed $default = null): mixed
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
            } elseif ($target instanceof ArrayAccess) {
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
     * Взорвите аргументы "value" и "key", переданные static::getPluck()
     *
     * @param array|string $value
     * @param array|string|null $key
     * @return array
     */
    protected static function explodePluckParameters(array|string $value, array|string|null $key): array
    {
        $value = is_string($value) ? explode('.', $value) : $value;
        $key = is_null($key) || is_array($key) ? $key : (is_string($key) ? explode('.', $key) : null);

        return [$value, $key];
    }

    /**
     * Получить элемент из массива с использованием нотации "точка"
     *
     * @param array|int|string|null $key
     * @param array $array
     * @param mixed $default
     * @return mixed
     */
    public static function get(array|int|string|null $key, array $array = [], mixed $default = null): mixed
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
     * Получение числа из массива с использованием нотации "точка"
     *
     * @note используйте если вам по логике всегда нужно число!
     *
     * @param string $key
     * @param array $array
     * @param int $default
     * @return int
     */
    public static function getGreaterZero(string $key, array $array = [], int $default = 1): int
    {
        $int = (int)static::get($key, $array, $default);

        return ($int > 0) ? $int : $default;
    }

    /**
     * Проверяет, существует ли данный ключ в предоставленном массиве
     *
     * @param int|string $key
     * @param array|ArrayAccess $array
     * @return bool
     */
    public static function exists(int|string $key, array|ArrayAccess $array): bool
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    /**
     * Проверить, присутствует ли элемент в массиве с помощью "точки"
     *
     * @param float|int|string $key
     * @param array $array
     * @param bool $dot флаг разбития строки символом точки для поиска под значений
     * @return bool
     */
    public static function has(string|int|float $key, array $array = [], bool $dot = true): bool
    {
        if (empty($array)) {
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
     * Проверка наличие списка ключей в массиве (возможна проверка вложенного массива с помощью "точки")
     *
     * @param array $key
     * @param array $array
     * @return bool
     */
    public static function hasKeys(array $key, array $array = []): bool
    {
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
     * Возвращает ключи из списка, где значение равно указанному
     *
     * @param mixed $value
     * @param array $array
     * @return array
     */
    public static function getSearchKeys(mixed $value, array $array = []): array
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
     * @param array $values
     * @param array $array
     * @return bool
     */
    public static function hasValues(array $values, array $array = []): bool
    {
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
     * Сгладьте многомерный массив на один уровень
     *
     * @param array $array
     * @param int $depth максимальный уровень глубины для алгоритма обхода массива (0 = INF)
     * @return array
     */
    public static function getFlatten(array $array = [], int $depth = 0): array
    {
        if (! is_array($array)) {
            return [];
        }

        return array_reduce($array, function ($result, $item) use ($depth) {
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
     * @param array $array
     * @param string $separator
     * @param string $prefix
     * @return array
     */
    public static function getSimplify(array $array = [], string $separator = "_", string $prefix = ""): array
    {
        $return = [];
        $recFunction = function ($fun, $separator, $prefix, $array = [], array $keys = [], &$return = []) {
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
     * @param int $depth максимальный уровень глубины для алгоритма обхода массива (0 = INF)
     * @return void
     */
    public static function flatten(array &$array = [], int $depth = 0): void
    {
        $array = static::getFlatten($array, $depth);
    }

    /**
     * Определяет, является ли массив ассоциативным
     *
     * Массив "ассоциативный", если он не имеет последовательных цифровых клавиш, начиная с нуля.
     *
     * @param array $array
     * @return bool
     */
    public static function isAssoc(array $array): bool
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    /**
     * Sort the array using the given callback.
     *
     * @param array $array
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
    public static function getSortRecursive(array $array): array
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
     * @param Closure|string $field
     * @param int $options
     * @param bool $descending
     * @return array
     */
    public static function sortBy(
        array $array,
        Closure|string $field,
        int $options = SORT_REGULAR,
        bool $descending = false
    ): array {
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
     * @param Closure|string $field
     * @param int $options
     * @return array
     */
    public static function sortByDesc(array $array, Closure|string $field, int $options = SORT_REGULAR): array
    {
        return static::sortBy($array, $field, $options, true);
    }

    /**
     * Фильтруйте массив, используя данный обратный вызов.
     *
     * @param array $array
     * @param callable $callback
     * @return array
     */
    public static function getWhere(array $array, callable $callback): array
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Если указанное значение не является массивом, оберните в массив.
     *
     * @param mixed $value
     * @return array
     */
    public static function getWrap(mixed $value = null): array
    {
        if (is_null($value)) {
            return [];
        }

        return ! is_array($value) ? [$value] : $value;
    }

    /**
     * @param string $delimiter
     * @param string $string
     * @param array|null $delete
     * @return array
     */
    public static function explode(string $delimiter, string $string, ?array $delete = ['', 0, null]): array
    {
        return VarArray::getRemove(explode($delimiter, $string), VarHelper::getArrayWrap($delete, false));
    }

    /**
     * Удаляет в массиве один или несколько ключей из переданных значений
     *
     * @note в удаляемых ключах допускается точка для вложенного действия
     *
     * @param array|string $keys ключи которые надо исключить
     * @param array $array массив в котором убираем значения по ключам
     * @return void
     */
    public static function itemsExcept(array|string $keys, array &$array): void
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
     * Удаляет в массиве один или несколько ключей из переданных значений
     *
     * @note в удаляемых ключах допускается точка для вложенного действия
     *
     * @param array|string $keys ключи которые надо исключить
     * @param array $array массив в котором убираем значения по ключам
     * @return array
     */
    public static function getItemsExcept(array|string $keys = [], array $array = []): array
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
     * Удаляет в массиве один или несколько ключей из переданных значений
     *
     * @note в удаляемых ключах допускается точка для вложенного действия
     *
     * @param array|string $keys ключи которые надо исключить
     * @param array $array массив в котором убираем значения по ключам
     * @return void
     */
    public static function except(array|string $keys, array &$array): void
    {
        static::forget($keys, $array);
    }

    /**
     * Возвращает все элементы, кроме тех, чьи ключи указаны в передаваемом массиве
     *
     * @note в удаляемых ключах допускается точка для вложенного действия
     *
     * @param array|string $keys ключи которые надо исключить
     * @param array $array массив в котором убираем значения по ключам
     * @return array
     */
    public static function getExcept(array|string $keys = [], array $array = []): array
    {
        static::forget($keys, $array);

        return $array;
    }

    /**
     * Удаляет в массиве один или несколько ключей из переданных значений
     *
     * @note в удаляемых ключах допускается точка для вложенного действия
     * @note улучшенный вариант without() но взят из ларавеля и нужно переписать!
     *
     * @param array|string $keys ключи которые надо исключить
     * @param array $array массив в котором убираем значения по ключам
     * @return void
     */
    public static function forget(array|string $keys, array &$array): void
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
     * @param array $array
     * @param callable|null $callback
     * @param mixed $default
     * @return mixed
     */
    public static function getFirst(array $array = [], callable $callback = null, mixed $default = null): mixed
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
     * @param array $array
     * @param callable|null $callback
     * @param mixed $default
     * @return mixed
     */
    public static function getSecond(array $array = [], callable $callback = null, mixed $default = null): mixed
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
     * @param array $array
     * @param callable|null $callback
     * @param mixed $default
     * @return mixed
     */
    public static function getLast(array $array = [], callable $callback = null, mixed $default = null): mixed
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
     * @param array $array
     * @param callable|null $callback
     * @param mixed $default
     * @return mixed
     */
    public static function getLastKey(array $array = [], callable $callback = null, mixed $default = null): mixed
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
     * @param string $key ключах допускается точка для вложенного действия
     * @param array $arr список, который будет группироваться по указанному ключу
     * @param bool $multiple флаг группировки с подмножеством значений. Не работает при вложенных группировка с точкой!
     * @return array
     */
    public static function getGroupBy(string $key, array $arr = [], bool $multiple = false): array
    {
        $result = [];
        $keys = VarStr::explode(".", $key, ['']);
        $depth = count($keys);

        if (count($arr) > 0) {
            if ($depth > 1) {

                // Группируем значения по первому значению
                $result = self::getGroupBy($keys[0], $arr, $multiple);

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
                    $result[$k1] = static::getGroupBy(
                        $keys[0],
                        $group2,
                        $multiple
                    ); // важно передать изначальный $multiple!
                }

                $key3 = array_shift($keys); // Извлекаю ключ по которому была группировка

                // Проверка наличие вложенной группировки по третьему значению
                if (count($keys) >= 1) {
                    // Прохожу по сгруппированному списку
                    foreach ($result as $k1 => $rows2) {
                        foreach ($rows2 as $k2 => $rows3) {
                            $group3 = [];

                            foreach ($arr as $list) {
                                if (key_exists($key2, $list)
                                    && $k1 == $list[$key2]
                                    && key_exists($key3, $list)
                                    && $k2 == $list[$key3]) {
                                    $group3[] = $list;
                                }
                            }

                            // Группирую список по вложенному ключу
                            $result[$k1][$k2] = static::getGroupBy(
                                $keys[0],
                                $group3,
                                $multiple
                            ); // важно передать изначальный $multiple!
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
                                    if (key_exists($key2, $list)
                                        && $k1 == $list[$key2]
                                        && key_exists($key3, $list)
                                        && $k2 == $list[$key3]
                                        && key_exists($key4, $list)
                                        && $k3 == $list[$key4]) {
                                        $group4[] = $list;
                                    }
                                }

                                // Группирую список по вложенному ключу
                                $result[$k1][$k2][$k3] = static::getGroupBy(
                                    $keys[0],
                                    $group4,
                                    $multiple
                                ); // важно передать изначальный $multiple!
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
     * @param string $groupKey ключ по которому группируются поля
     * @param array $arr список, который перебираем
     * @param array|string|null $with ключ, который будет сохранен. Если не указать, то будет сохранен ключ от группировки
     * @param bool $multiple флаг группировки с подмножеством значений
     * @return array
     */
    public static function getGroupWith(
        string $groupKey,
        array $arr = [],
        array|string $with = null,
        bool $multiple = false
    ): array {
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
     * @note если элементов было больше указанного числа, то они не удаляются из результата
     *
     * @param array $list список
     * @param int $length длина будущего списка
     * @param mixed $default значение для элементов которые будут созданы как недостающие
     * @return array
     */
    public static function getPadList(array $list, int $length = 0, mixed $default = null): array
    {
        $list = array_pad($list, $length, $default);

        return array_values($list);
    }

    /**
     * Удаление переданных значений из массива
     *
     * @param array $arr
     * @param array|null $delete
     * @return array
     */
    public static function getRemove(array $arr, ?array $delete = ['', 0, null]): array
    {
        return array_diff($arr, VarHelper::getArrayWrap($delete, false));
    }

    /**
     * Удаление переданных значений по ссылке из указанного массива
     *
     * @param array $arr
     * @param array|null $delete
     * @return void
     */
    public static function remove(array &$arr, ?array $delete = ['', 0, null]): void
    {
        $arr = array_diff($arr, VarHelper::getArrayWrap($delete, false));
    }

    /**
     * Возвращает из массива данные по указанным ключам
     *
     * @param array|float|int|string $keys
     * @param array $arr
     * @param bool $required флаг обязательного наличия ключей при возврате равное указанному
     * @param mixed $default значение используется совместно с $required = TRUE
     * @return array
     */
    public static function getExtract(
        array|float|int|string $keys,
        array $arr = [],
        bool $required = true,
        mixed $default = null
    ): array {
        static::extract($keys, $arr, $required, $default);

        return $arr;
    }

    /**
     * Извлекает (по ссылке) из массива данные по указанным ключам
     *
     * @note в результате работы в массиве $arr останутся только данные с ключами которые указаны с $keys
     *
     * @param array|bool|float|int|string|null $keys
     * @param array $arr
     * @param bool $required флаг обязательного наличия ключей при возврате равное указанному
     * @param mixed $default значение используется совместно с $required = TRUE
     * @return void
     */
    public static function extract(
        array|bool|float|int|string|null $keys,
        array &$arr = [],
        bool $required = true,
        mixed $default = null
    ): void {
        // Сценарий плоского извлечения
        if (is_array($keys) && count($keys) > 0 && is_array($return = [])) {
            foreach ($keys as $key) {
                if (array_key_exists($key, $arr)) {
                    $return[$key] = $arr[$key];
                    unset($arr[$key]);

                } elseif ($required) {
                    $return[$key] = $default;
                }
            }

            $arr = $return;

        } else {
            $return = $default;

            if (is_string($keys) && mb_strlen($keys) > 0) {
                $parts = explode('.', $keys);
            } elseif (is_numeric($keys)) {
                $parts = [$keys];
            }

            if (isset($parts)) {
                $last = $parts[count($parts) - 1];

                while (count($parts) >= 1) {
                    $part = array_shift($parts); // Извлекает первый элемент массива

                    // переходим на уровень ниже
                    if ($last != $part && is_array($arr[$part])) {
                        $arr = &$arr[$part];
                        continue;

                    } else {
                        // сюда попадают когда дошли до нужного уровня или массив не имеет больше потомков
                        $parts = [];

                        // смотрим наличие последнего ключа для проверки и удаление по нему его значений
                        if ($last == $part && array_key_exists($last, $arr)) {
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
    }

    /**
     * Обходит коллекцию и в уже её значениях массива извлекает один или несколько ключей из переданных значений
     *
     * @note в извлекаемых ключах допускается точка для вложенного действия
     *
     * @param array|float|int|string $keys ключи которые надо извлечь и оставить
     * @param array $array массив в котором производим извлечения
     * @return void
     */
    public static function itemsExtract(array|float|int|string $keys, array &$array): void
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
     * Извлекает в массиве один или несколько ключей из переданных значений
     *
     * @note в извлекаемых ключах допускается точка для вложенного действия
     *
     * @param array|float|int|string $keys ключи которые надо исключить
     * @param array $array массив в котором убираем значения по ключам
     * @return array
     */
    public static function getItemsExtract(array|float|int|string $keys = [], array $array = []): array
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
     * @note: поскольку функция each() объявлена УСТАРЕВШЕЙ начиная с PHP 7.2.0 и ее использование крайне не рекомендовано
     *
     * @param array $arr
     * @return array|false
     */
    public static function each(array $arr): array|false
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
     * @param bool $recursive
     * @return array
     */
    public static function stripSlashes(array $arr = [], bool $recursive = false): array
    {
        $return = [];

        if (is_array($arr) && count($arr) > 0) {
            reset($arr);

            foreach ($arr as $key => $item) {
                if ($recursive && is_array($item)) {
                    $return[$key] = static::stripslashes($item, $recursive);

                } else {
                    $return[$key] = stripslashes(! is_string($item) ? VarStr::getMakeString($item) : $item);
                }
            }
        }

        return $return;
    }

    /**
     * Удаляет пробелы из начала и конца строки (или другие символы при передаче их вторым параметром)
     *
     * @param array $arr
     * @param string $removeChar список символов для удаления
     * @param bool $recursive флаг для обхода потомков
     * @return array
     */
    public static function trim(array $arr = [], string $removeChar = " \t\n\r\0\x0B", bool $recursive = false): array
    {
        $return = [];

        if (is_array($arr) && count($arr) > 0) {
            reset($arr);

            foreach ($arr as $key => $item) {
                if ($recursive && is_array($item)) {
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
     * @param array $arr
     * @param array|string $char
     * @param array|string $replace
     * @param bool $recursive флаг для обхода потомков
     * @return array
     */
    public static function getRemovingDoubleChar(
        array $arr = [],
        array|string $char = ' ',
        array|string $replace = ' ',
        bool $recursive = false
    ): array {
        $return = [];

        if (is_array($arr) && count($arr) > 0) {
            reset($arr);

            foreach ($arr as $key => $item) {
                if ($recursive && is_array($item)) {
                    $return[$key] = static::getRemovingDoubleChar($item, $char, $replace, $recursive);

                } else {
                    $return[$key] = VarStr::getRemovingDoubleChar($item, $char, $replace);
                }
            }
        }

        return $return;
    }

    /**
     * Преобразование значений массива в целое число с проверкой на минимальное значение
     *
     * @note: возможны отрицательные значения!
     *
     * @param array $data
     * @param int $default
     * @param bool $recursive
     * @return array
     */
    public static function getMinInt(array $data, int $default = 0, bool $recursive = false): array
    {
        $default = VarInt::getMakeInteger($default);

        if (count($data) > 0 && is_array($return = [])) {
            reset($data);

            foreach ($data as $key => $item) {
                if ($recursive && is_array($item)) {
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
     * @param int $default
     * @param bool $recursive
     * @return void
     */
    public static function minInt(array &$data, int $default = 0, bool $recursive = false): void
    {
        $data = static::getMinInt($data, $default, $recursive);
    }

    /**
     * Преобразование значений массива в целое число с проверкой его на максимальное значение
     *
     * @note: возможны отрицательные значения!
     *
     * @param array $data
     * @param int $max число предела
     * @param bool $toDefault флаг преобразования числа вышедшего за пределы в default или max
     * @param int $default
     * @param bool $recursive
     * @return array
     */
    public static function getMaxInt(
        array $data,
        int $max = 0,
        bool $toDefault = true,
        int $default = 0,
        bool $recursive = false
    ): array {
        $default = VarInt::getMakeInteger($default);

        if (count($data) > 0 && is_array($return = [])) {
            reset($data);

            foreach ($data as $key => $item) {
                if ($recursive && is_array($item)) {
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
     * @param int $max число предела
     * @param bool $toDefault флаг преобразования числа вышедшего за пределы в default или max
     * @param int $default
     * @param bool $recursive
     * @return void
     */
    public static function maxInt(
        array &$data,
        int $max = 0,
        bool $toDefault = true,
        int $default = 0,
        bool $recursive = false
    ): void {
        $data = static::getMaxInt($data, $max, $toDefault, $default, $recursive);
    }

    /**
     * Оставить подмножество элементов из заданного массива
     *
     * @param array|string $haystack список с допустимых значений
     * @param array $array список, который фильтруем
     * @return array
     */
    public static function getOnly(array|string $haystack, array $array): array
    {
        return array_intersect_key($array, array_flip((array)$haystack));
    }

    /**
     * Оставить подмножество элементов из заданного массива
     *
     * @param array|string $haystack список с допустимых значений
     * @param array $array список, который фильтруем
     * @return void
     */
    public static function only(array|string $haystack, array &$array): void
    {
        $array = static::getOnly((array)$haystack, $array);
    }

    /**
     * Оставить указанное подмножество элементов в списках
     *
     * @param array|string $haystack список с допустимых значений
     * @param array $array список, который фильтруем
     * @return array
     */
    public static function getItemsOnly(array|string $haystack, array $array): array
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
     * Оставить указанное подмножество элементов в списках
     *
     * @param array|string $haystack список с допустимых значений
     * @param array $array список, который фильтруем
     * @return void
     */
    public static function itemsOnly(array|string $haystack, array &$array): void
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
     * @param bool $recursive
     * @return array
     * @throws Exception
     */
    public static function ucfirst(array $data, bool $recursive = false): array
    {
        if (count($data) > 0 && is_array($return = [])) {
            reset($data);

            foreach ($data as $key => $item) {
                if ($recursive && is_array($item)) {
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
     * Преобразует все символы в верхний регистр
     *
     * @param array $arr
     * @param bool $recursive флаг для обхода потомков
     * @return array
     * @throws Exception
     */
    public static function getUpper(array $arr = [], bool $recursive = false): array
    {
        if (is_array($arr) && count($arr) > 0 && is_array($return = [])) {
            reset($arr);

            foreach ($arr as $key => $item) {
                if ($recursive && is_array($item)) {
                    $return[$key] = static::getUpper($item, $recursive);

                } else {
                    $return[$key] = VarStr::getUpper($item);
                }
            }
        }

        return [];
    }

    /**
     * Преобразует все символы в нижний регистр
     *
     * @param array $arr
     * @param bool $recursive флаг для обхода потомков
     * @return array
     * @throws Exception
     */
    public static function getLower(array $arr = [], bool $recursive = false): array
    {
        $return = [];

        if (is_array($arr) && count($arr) > 0) {
            reset($arr);

            foreach ($arr as $key => $item) {
                if ($recursive && is_array($item)) {
                    $return[$key] = static::getLower($item, $recursive);

                } else {
                    $return[$key] = VarStr::getLower($item);
                }
            }
        }

        return $return;
    }

    /**
     * Получить один или несколько элементов случайным образом из массива
     *
     * @param array $arr список из которого выбираем
     * @param int $amount какое количество элементов вернуть
     * @param bool $saveKey
     * @return array
     */
    public static function getRandomItems(array $arr = [], int $amount = 1, bool $saveKey = true): array
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
     * Проверка наличия в массиве значений более чем указано
     *
     * @param array $arr
     * @param int $length
     * @return bool
     */
    public static function isNotEmpty(array $arr, int $length = 1): bool
    {
        if (count($arr) >= $length) {
            return true;
        }

        return false;
    }
}
