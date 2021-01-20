<?php

namespace Warkhosh\Variable;

use Warkhosh\Variable\Traits\Singleton;

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
    protected $method;


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
     * @param string $method
     * @return bool
     */
    static public function isMethod($method = null)
    {
        $method = empty($_SERVER['REQUEST_METHOD']) ? "get" : strtolower($_SERVER['REQUEST_METHOD']);

        if ($method === strtolower($method)) {
            return true;
        }

        return false;
    }


    /**
     * Возвращает все или указанную переменную
     *
     * @param string | array
     * @return mixed
     */
    public static function get()
    {
        if (count(func_get_args()) >= 1 && ! is_null($varName = func_get_arg(0))) {
            $varName = is_array($varName) ? $varName : (string)$varName;
            $initDefault = false;
            $default = null;

            if (count(func_get_args()) >= 2) {
                $default = func_get_arg(1);
                $initDefault = true;
            }

            return static::getVariable($_GET, $varName, $initDefault, $default);
        }

        return $_GET;
    }


    /**
     * Возвращает все или указанную переменную
     *
     * @param string | array
     * @return mixed
     */
    public static function post()
    {
        if (count(func_get_args()) >= 1 && ! is_null($varName = func_get_arg(0))) {
            $varName = is_array($varName) ? $varName : (string)$varName;
            $initDefault = false;
            $default = null;

            if (count(func_get_args()) >= 2) {
                $default = func_get_arg(1);
                $initDefault = true;
            }

            return static::getVariable($_POST, $varName, $initDefault, $default);
        }

        return $_POST;
    }

    /**
     * Возвращает все или указанную переменную
     *
     * @param string | array
     * @return mixed
     */
    public static function put()
    {
        $_PUT = isset($GLOBALS['_PUT']) ? $GLOBALS['_PUT'] : [];

        if (count(func_get_args()) >= 1 && ! is_null($varName = func_get_arg(0))) {
            $varName = is_array($varName) ? $varName : (string)$varName;
            $initDefault = false;
            $default = null;

            if (count(func_get_args()) >= 2) {
                $default = func_get_arg(1);
                $initDefault = true;
            }

            return static::getVariable($_PUT, $varName, $initDefault, $default);
        }

        return $_PUT;
    }

    /**
     * Возвращает все или указанную переменную
     *
     * @param string | array
     * @return mixed
     */
    public static function delete()
    {
        $_PUT = isset($GLOBALS['_DELETE']) ? $GLOBALS['_DELETE'] : [];

        if (count(func_get_args()) >= 1 && ! is_null($varName = func_get_arg(0))) {
            $varName = is_array($varName) ? $varName : (string)$varName;
            $initDefault = false;
            $default = null;

            if (count(func_get_args()) >= 2) {
                $default = func_get_arg(1);
                $initDefault = true;
            }

            return static::getVariable($_PUT, $varName, $initDefault, $default);
        }

        return $_PUT;
    }


    /**
     * Возвращает все или указанную переменную
     *
     * @param string | array
     * @return mixed
     */
    public static function file()
    {
        if (count(func_get_args()) >= 1 && ! is_null($varName = func_get_arg(0))) {
            $varName = is_array($varName) ? $varName : (string)$varName;
            $initDefault = false;
            $default = null;

            if (count(func_get_args()) >= 2) {
                $default = func_get_arg(1);
                $initDefault = true;
            }

            return static::getVariable($_FILES, $varName, $initDefault, $default);
        }

        return $_FILES;
    }


    /**
     * Возвращает все или указанную переменную
     *
     * @param string | array
     * @return mixed
     */
    public static function any()
    {
        $_PUT = isset($GLOBALS['_PUT']) ? $GLOBALS['_PUT'] : [];
        $_DELETE = isset($GLOBALS['_DELETE']) ? $GLOBALS['_DELETE'] : [];

        if (count(func_get_args()) >= 1 && ! is_null($varName = func_get_arg(0))) {
            $varName = is_array($varName) ? $varName : (string)$varName;
            $initDefault = false;
            $default = null;

            if (count(func_get_args()) >= 2) {
                $default = func_get_arg(1);
                $initDefault = true;
            }

            if (is_array($varName)) {
                $get = static::getVariable($_GET, $varName);
                $put = static::getVariable($_PUT, $varName);
                $delete = static::getVariable($_DELETE, $varName);
                $post = static::getVariable($_POST, $varName, $initDefault, $default);

                return array_merge((array)$get, (array)$put, (array)$delete, (array)$post);
            }

            $get = static::getVariable($_GET, $varName);
            $put = static::getVariable($_PUT, $varName);
            $delete = static::getVariable($_DELETE, $varName);
            $post = static::getVariable($_POST, $varName);

            $var = ! empty($GLOBALS['_PUT']) ? $put : (empty($GLOBALS['_DELETE']) ? $post : $delete);
            $var = empty($var) ? $get : $var;

            return is_null($var) && $initDefault ? $default : $var;
        }

        return array_merge($_GET, $_PUT, $_POST, $_DELETE);
    }
}