<?php

namespace Warkhosh\Variable;

use Warkhosh\Variable\Traits\VariableMethod;

/**
 * Class Variable
 *
 * @package Ekv\Framework\Components\Support
 */
class Variable
{
    use VariableMethod;

    /**
     * @var array | string | integer | float | null - $data
     */
    protected $data;

    /**
     * Тип переменной: массив значений или одно значение
     *
     * @var mixed $data
     */
    protected $dataType = 'value';

    /**
     * Default значение
     *
     * @note надо следить что-бы в значение по умолчанию не передавали массив или объект ( если этого не требует логика )
     *
     * @var array | string | integer | float | null $default
     */
    protected $default;

    /**
     * Для замены пустых строк при отдаче значений
     *
     * @var string
     */
    protected $convertAnEmptyString = '';


    /**
     * Variable constructor.
     *
     * @param array | string | integer | float - $data
     */
    public function __construct($data = null)
    {
        $this->data = $data;

        if (count(func_get_args()) >= 2) {
            $this->byDefault(func_get_arg(1));
        }
    }


    /**
     * Установка значения для переменной
     *
     * @param mixed $data
     * @return $this
     */
    public function set($data)
    {
        if (is_array($data) || is_string($data) || is_numeric($data) || is_float($data)) {
            $this->data = $data;

        } else if ($data instanceof \Traversable) {
            $this->data = iterator_to_array($data);
        }

        return $this;
    }


    /**
     * Возвращает значение переменной
     *
     * @note если не указать название или ключ переменной то будут возвращены все значения;
     *
     * @param integer $keys
     * @return mixed
     */
    public function get($keys = null)
    {
        $this->data = $this->getEmptyStringConversion($this->data);

        if (is_null($this->data)) {

            // если указали что переменная массив а default указали не массивом, оборачиваем его в массив
            if ($this->dataType === 'array' && ! is_array($this->default)) {
                return is_array($this->default) ? $this->default : [$this->default];
            }

            return $this->default === '' ? $this->convertAnEmptyString : $this->default;
        }

        // если ключа нет то возвращаем все
        if (is_null($keys)) {
            return $this->data;
        }

        // Если указали список ключей
        if (is_array($keys) && count($keys) > 0) {
            $return = [];

            // Перебираем массиве запрошенных значений и по каждому получаем результат что-бы отдать этот массив
            foreach ($keys as $key) {
                if (is_array($this->data) && array_key_exists($key, $this->data)) {
                    $return[$key] = $this->data[$key];
                } else {
                    $return[$key] = ($this->default === '' ? $this->convertAnEmptyString : $this->default);
                }
            }

            return $return;
        }

        // Если указали конкретный ключ а мы работаем с типом массив
        if (! is_array($keys) && $this->dataType === 'array') {
            // Пытаемся вернуть значение по ключу
            if (is_array($this->data) && array_key_exists($keys, $this->data)) {
                return $this->data[$keys];
            }

            return $this->default === '' ? $this->convertAnEmptyString : $this->default;
        }

        // если указали что переменная массив а default указали не массивом, оборачиваем его в массив
        if ($this->dataType === 'array' && ! is_array($this->default)) {
            return is_array($this->default) ? $this->default : [$this->default];
        }

        return $this->default === '' ? $this->convertAnEmptyString : $this->default;
    }


    /**
     * Возвращает все значения;
     *
     * @return array|string
     */
    public function all()
    {
        return $this->get();
    }


    /**
     * Переобход данных для проверки их строк на пустое значение и преобразование в иное
     *
     * @param null $data
     * @return array|null|string
     */
    protected function getEmptyStringConversion($data = null)
    {
        if ($this->convertAnEmptyString === '') {
            return $data;
        }

        if (is_array($data)) {
            foreach ($data as $key => $row) {
                $data[$key] = $this->getEmptyStringConversion($row);
            }

        } else if (is_string($data)) {
            $data = $data === '' ? $this->convertAnEmptyString : $data;
        }

        return $data;
    }
}