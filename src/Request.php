<?php

declare(strict_types=1);

namespace Warkhosh\Variable;

use Warkhosh\Singleton\Trait\Singleton;

/**
 * Class Request
 *
 * @package Warkhosh\Variable
 */
class Request
{
    use Singleton;

    /**
     * @var string $method
     */
    protected static string $method;


    /**
     * Request constructor
     */
    public function __construct()
    {
        static::$method = empty($_SERVER['REQUEST_METHOD']) ? "get" : strtolower($_SERVER['REQUEST_METHOD']);
        static::$method = in_array(static::$method, ['get', 'post', 'put', 'delete']) ? static::$method : 'get';
    }


    /**
     * Проверка названия текущего метода
     *
     * @param string|null $method
     * @return bool
     */
    public static function isMethod(?string $method = null): bool
    {
        if (static::$method === mb_strtolower((string)$method)) {
            return true;
        }

        return false;
    }


    /**
     * Возвращает все или указанную переменную
     *
     * @note если передали второй аргумент будет служить значениями по умолчанию
     *
     * @param array|int|string|null $varName
     * @param mixed|null $default
     * @return array|float|int|string|null
     */
    public static function get(
        array|int|string|null $varName = null,
        mixed $default = null
    ): array|float|int|string|null {
        if (is_null($varName)) {
            return $_GET;
        }

        $varName = is_array($varName) ? $varName : (string)$varName;
        $initDefault = false;

        if (count(func_get_args()) >= 2) {
            $initDefault = true;
        }

        return static::getVariable($_GET, $varName, $initDefault, $default);
    }


    /**
     * Возвращает все или указанную переменную
     *
     * @param array|int|string|null $varName
     * @param mixed|null $default
     * @return array|float|int|string|null
     */
    public static function post(
        array|int|string|null $varName = null,
        mixed $default = null
    ): array|float|int|string|null {
        if (is_null($varName)) {
            return $_POST;
        }

        $varName = is_array($varName) ? $varName : (string)$varName;
        $initDefault = false;

        if (count(func_get_args()) >= 2) {
            $initDefault = true;
        }

        return static::getVariable($_POST, $varName, $initDefault, $default);
    }

    /**
     * Возвращает все или указанную переменную
     *
     * @param array|int|string|null $varName
     * @param mixed|null $default
     * @return array|float|int|string|null
     */
    public static function put(
        array|int|string|null $varName = null,
        mixed $default = null
    ): array|float|int|string|null {
        $_PUT = $GLOBALS['_PUT'] ?? [];

        if (is_null($varName)) {
            return $_PUT;
        }

        $varName = is_array($varName) ? $varName : (string)$varName;
        $initDefault = false;

        if (count(func_get_args()) >= 2) {
            $initDefault = true;
        }

        return static::getVariable($_PUT, $varName, $initDefault, $default);
    }

    /**
     * Возвращает все или указанную переменную
     *
     * @param array|int|string|null $varName
     * @param mixed|null $default
     * @return array|float|int|string|null
     */
    public static function delete(
        array|int|string|null $varName = null,
        mixed $default = null
    ): array|float|int|string|null {
        $_DELETE = $GLOBALS['_DELETE'] ?? [];

        if (is_null($varName)) {
            return $_DELETE;
        }

        $varName = is_array($varName) ? $varName : (string)$varName;
        $initDefault = false;

        if (count(func_get_args()) >= 2) {
            $initDefault = true;
        }

        return static::getVariable($_DELETE, $varName, $initDefault, $default);
    }


    /**
     * Возвращает все или указанную переменную
     *
     * @param array|int|string|null $varName
     * @param mixed|null $default
     * @return array|int|string|null
     */
    public static function file(
        array|int|string|null $varName = null,
        mixed $default = null
    ): array|int|string|null {
        if (is_null($varName)) {
            return $_FILES;
        }

        $varName = is_array($varName) ? $varName : (string)$varName;
        $initDefault = false;

        if (count(func_get_args()) >= 2) {
            $initDefault = true;
        }

        return static::getVariable($_FILES, $varName, $initDefault, $default);
    }


    /**
     * Возвращает все или указанную переменную
     *
     * @param array|int|string|null $varName
     * @param mixed|null $default
     * @return array|float|int|string|null
     */
    public static function any(
        array|int|string|null $varName = null,
        mixed $default = null
    ): array|float|int|string|null {
        $_PUT = $GLOBALS['_PUT'] ?? [];
        $_DELETE = $GLOBALS['_DELETE'] ?? [];

        if (is_null($varName)) {
            return array_merge($_GET, $_POST, (array)$_DELETE, (array)$_PUT);
        }

        $_ALL = array_merge($_GET, (array)$_PUT, (array)$_DELETE, $_POST);
        $varName = is_array($varName) ? $varName : (string)$varName;
        $initDefault = false;

        if (count(func_get_args()) >= 2) {
            $initDefault = true;
        }

        return static::getVariable($_ALL, $varName, $initDefault, $default);
    }

    /**
     * @param array $storage хранилище с данными
     * @param array|string $varKey название переменной
     * @param bool $initDefault флаг для установки значения для неустановленной переменной значения по умолчанию
     * @param mixed|null $default значение по умолчанию
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
