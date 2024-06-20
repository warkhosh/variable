<?php

declare(strict_types=1);

namespace Warkhosh\Variable;

use Warkhosh\Component\Traits\Singleton;

/**
 * Class Request
 *
 * @package Warkhosh\Variable
 */
class Request extends Http
{
    use Singleton;

    /**
     * @var string $method
     */
    protected string $method;


    /**
     * Request constructor
     */
    public function __construct()
    {
        $this->method = empty($_SERVER['REQUEST_METHOD']) ? "get" : strtolower($_SERVER['REQUEST_METHOD']);
        $this->method = in_array($this->method, ['get', 'post', 'put', 'delete']) ? $this->method : 'get';

        parent::__construct();
    }


    /**
     * Проверка названия текущего метода
     *
     * @param string|null $method
     * @return bool
     */
    public static function isMethod(?string $method = null): bool
    {
        $requestMethod = empty($_SERVER['REQUEST_METHOD']) ? "get" : strtolower($_SERVER['REQUEST_METHOD']);

        if ($requestMethod === strtolower((string)$method)) {
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
}
