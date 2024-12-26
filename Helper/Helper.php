<?php

declare(strict_types=1);

namespace Warkhosh\Variable\Helper;

use Exception;

/**
 * Class Helper
 *
 * @package Warkhosh\Variable\Helper
 */
class Helper
{
    /**
     * Проверка истинности значения
     *
     * @param mixed $var
     * @param bool $strict
     * @return bool
     * @throws Exception
     * @deprecated Методы теперь в VarBool
     */
    public static function isTrue(mixed $var = null, bool $strict = false): bool
    {
        throw new Exception("Методы теперь в VarBool!");
    }

    /**
     * Проверка истинности значения
     *
     * @param mixed $var
     * @param bool $strict
     * @return bool
     * @throws Exception
     * @deprecated Методы теперь в VarBool
     */
    public static function isFalse(mixed $var = null, bool $strict = false): bool
    {
        throw new Exception("Методы теперь в VarBool!");
    }
}
