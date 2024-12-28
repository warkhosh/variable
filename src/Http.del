<?php

declare(strict_types=1);

namespace Warkhosh\Variable;

/**
 * Class Http
 *
 * @package Warkhosh\Variable
 */
class Http
{
    /**
     * Http constructor
     */
    public function __construct()
    {
        // ...
    }


    /**
     * Возвращает все или указанную переменную
     *
     * @note может принимать один или два аргумента.
     * Первый аргумент это название ключа в переменной $_SERVER в формате string.
     * Второй аргумент это default значение если указали не существующий ключ.
     *
     * @return mixed
     */
    public static function server(): mixed
    {
        if (count(func_get_args()) >= 1 && ! is_null($key = func_get_arg(0))) {
            $initDefault = false;
            $default = null;

            if (count(func_get_args()) >= 2) {
                $default = func_get_arg(1);
                $initDefault = true;
            }

            return self::getVariable($_SERVER, (string)$key, $initDefault, $default);
        }

        return $_SERVER;
    }


    /**
     * @param array $storage - хранилище с данными
     * @param array|string $varKey - название переменной
     * @param bool $initDefault - флаг для установки значения для неустановленной переменной значения по умолчанию
     * @param mixed|null $default - значение по умолчанию
     * @return mixed
     */
    protected static function getVariable(
        array $storage,
        array|string $varKey,
        bool $initDefault = false,
        mixed $default = null
    ): mixed {
        if (is_string($varKey)) {
            if (isset($storage[$varKey]) && key_exists($varKey, $storage)) {
                return $storage[$varKey];

            } elseif ($initDefault) {
                return $default;
            }

        } elseif (is_array($varKey) && is_array($return = [])) {

            foreach ($varKey as $key) {
                if (isset($storage[$key]) && key_exists($key, $storage)) {
                    $return[$key] = $storage[$key];

                } elseif ($initDefault) {
                    $return[$key] = $default;
                }
            }

            return $return;
        }

        return null;
    }
}
