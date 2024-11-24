# Variable
Работа с переменными

### VarArray
Класс для работы с переменными типа Array.  
Со списком методов можно ознакомиться в Warkhosh\Variable\VarArray.php

### VarFloat
Класс для работы с переменными типа Float или Double.  
Со списком методов можно ознакомиться в Warkhosh\Variable\VarFloat.php

### VarStr
Класс для работы с переменными типа String.  
Со списком методов можно ознакомиться в Warkhosh\Variable\VarStr.php

### VarInt
Класс для работы с переменными типа Integer.  
Со списком методов можно ознакомиться в Warkhosh\Variable\VarInt.php

### VarBool
Класс для работы с переменными типа Boolean.  
Со списком методов можно ознакомиться в Warkhosh\Variable\VarBool.php

### Examples:

Преобразование значения в число с плавающей точкой
```php
echo Warkhosh\Variable\VarFloat::getMake("159.127");
```

Преобразование значения в число с плавающей точкой с округлением десятичных в меньшую сторону до двух знаков.
```php
echo Warkhosh\Variable\VarFloat::getMake("159.127", 2, "downward", $default = 0.0);
```

Преобразование значения в число с плавающей точкой в положительном диапазоне значений.
```php
echo Warkhosh\Variable\VarFloat::getMakePositive("15.1", 2, "upward", $default = 0.0);
```

## Дополнительные функции
```php
if (! function_exists('get')) {
    /**
     * @param string $varName
     * @param mixed $default
     * @return Variable
     * @package Warkhosh\Variable
     * @version 1.1
     */
    function get(string $varName, mixed $default)
    {
        $value = \Warkhosh\Variable\Request::get($varName);
        $appVariable = new \Warkhosh\Variable\Variable($value, $default);

        return $appVariable;
    }
}


if (! function_exists('post')) {
    /**
     * @param string $varName
     * @param mixed $default
     * @return Variable
     * @package Warkhosh\Variable
     * @version 1.1
     */
    function post(string $varName, mixed $default)
    {
        $value = \Warkhosh\Variable\Request::post($varName);
        $appVariable = new \Warkhosh\Variable\Variable($value, $default);

        return $appVariable;
    }
}

if (! function_exists('put')) {
    /**
     * @param string $varName
     * @param mixed $default
     * @return Variable
     * @package Warkhosh\Variable
     * @version 1.1
     */
    function put(string $varName, mixed $default)
    {
        $value = \Warkhosh\Variable\Request::put($varName);
        $appVariable = new \Warkhosh\Variable\Variable($value, $default);

        return $appVariable;
    }
}

if (! function_exists('delete')) {
    /**
     * @param string $varName
     * @param mixed $default
     * @return Variable
     * @package Warkhosh\Variable
     * @version 1.1
     */
    function delete(string $varName, mixed $default)
    {
        $value = \Warkhosh\Variable\Request::delete($varName);
        $appVariable = new \Warkhosh\Variable\Variable($value, $default);

        return $appVariable;
    }
}


if (! function_exists('request')) {
    /**
     * @param string $varName
     * @param mixed $default
     * @return Variable
     * @package Warkhosh\Variable
     * @version 1.1
     */
    function request(string $varName, mixed $default)
    {
        $value = \Warkhosh\Variable\Request::any($varName);
        $appVariable = new \Warkhosh\Variable\Variable($value, $default);

        return $appVariable;
    }
}
```
