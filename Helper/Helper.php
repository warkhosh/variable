<?php

declare(strict_types=1);

namespace Warkhosh\Variable\Helper;

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
     */
    public static function isTrue(mixed $var = null, bool $strict = false): bool
    {
        if ($var === true) {
            return true;
        }

        if (is_array($var) || is_object($var)) {
            return false;
        }

        if ($strict === false) {
            if ((int)$var === 1 || mb_strtolower(trim((string)$var)) === 'true') {
                return true;
            }
        }

        return false;
    }

    /**
     * Проверка истинности значения
     *
     * @param mixed $var
     * @param bool $strict
     * @return bool
     */
    public static function isFalse(mixed $var = null, bool $strict = false): bool
    {
        if ($var === false) {
            return true;
        }

        if (is_array($var) || is_object($var)) {
            return false;
        }

        if ($strict === false) {
            if (((int)$var === 0 || mb_strtolower(trim((string)$var)) === 'false')) {
                return true;
            }
        }

        return false;
    }
}
