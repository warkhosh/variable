<?php

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
     * @param string | array
     * @return mixed
     */
    public static function server()
    {
        if (count(func_get_args()) >= 1 && ! is_null($varName = func_get_arg(0))) {
            $initDefault = false;
            $default = null;

            if (count(func_get_args()) >= 2) {
                $default = func_get_arg(1);
                $initDefault = true;
            }

            return self::getVariable($_SERVER, (string)$varName, $initDefault, $default);
        }

        return $_SERVER;
    }


    /**
     * @param array          $storage     - хранилище с данными
     * @param string | array $varName     - название переменной
     * @param bool           $initDefault - флаг для установки значения для неустановленной переменной значения по умолчанию
     * @param null           $default     - значение по умолчанию
     * @return array|null
     */
    static protected function getVariable(array $storage, $varName, $initDefault = false, $default = null)
    {
        if (is_string($varName)) {
            if (isset($storage[$varName]) && key_exists($varName, $storage)) {
                return $storage[$varName];

            } elseif ($initDefault) {
                return $default;
            }

        } elseif (is_array($varName) && is_array($return = [])) {

            foreach ($varName as $key) {
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