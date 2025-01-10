<?php

namespace Warkhosh\Variable\Trait;

/**
 * Trait VariableData
 *
 * @package Ekv\Framework\Components\Support\Traits
 */
trait VariableData
{
    /**
     * @var array|float|int|string|null
     */
    protected array|float|int|string|null $data = null;

    /**
     * Default значение
     *
     * @note надо следить что-бы в значение по умолчанию не передавали массив или объект (если этого не требует логика)
     *
     * @var array|float|integer|string|null $default
     */
    protected array|float|int|string|null $default = null;

    /**
     * Служит значением которое будет заменять пустые строки если они получились при условии преобразования данных в строки
     *
     * @var string
     */
    protected string $convertAnEmptyString = '';
}
