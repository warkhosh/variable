<?php

namespace Warkhosh\Variable\Traits;

/**
 * Trait Singleton
 *
 * @package Warkhosh\Variable\Traits
 */
trait Singleton
{
    private static $instances = [];

    /**
     * Защищаем от создания через new Singleton
     */
    protected function __construct()
    {
        // ...
    }


    /**
     * Защищаем от создания через клонирование
     */
    protected function __clone()
    {
        // ...
    }


    /**
     * Защищаем от создания через unserialize
     *
     * @throws \Exception
     */
    private function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }


    /**
     * Статический метод, управляющий доступом к экземпляру одиночки.
     *
     * @return $this
     */
    public static function getInstance()
    {
        $class = static::class;

        if (! isset(static::$instances[$class])) {
            static::$instances[$class] = new static;
        }

        return static::$instances[$class];
    }
}