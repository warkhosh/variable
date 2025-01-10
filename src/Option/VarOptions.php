<?php

declare(strict_types=1);

namespace Warkhosh\Variable\Option;

class VarOptions
{
    public const TRIM_REMOVE_CHAR = " \t\n\r\f\v\0\x0B";
    public const REMOVE_SPECIAL_SYMBOL = ["\t", "\n", "\r", "\f", "\v", "\0"];
}
