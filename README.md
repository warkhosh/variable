# Variable
Работа с переменными

### VarArray
Класс для работы с переменной типа Array.		
Со списком методов можно ознакомиться в Warkhosh\Variable\VarArray.php

### VarFloat
Класс для работы с переменной типа Float или Double		
Со списком методов можно ознакомиться в Warkhosh\Variable\VarFloat.php

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
     * @param string $var
     *
     * @return \Warkhosh\Variable\Variable
     */
    function get($var)
    {
        $var = \Warkhosh\Variable\Request::get((string)$var);
        $appVariable = new \Warkhosh\Variable\Variable($var);

        // Если передан второй параметр то значит это значения по умолчанию
        if (count(func_get_args()) >= 2) {
            $appVariable->byDefault(func_get_arg(1));
        }

        // Если передан третий параметр то значит это тип переменной по умолчанию
        if (count(func_get_args()) === 3) {
            $method = func_get_arg(2);
            $appVariable->{$method}();
        } else {
            $appVariable->string();
        }

        return $appVariable;
    }
}


if (! function_exists('post')) {
    /**
     * @param string $var
     *
     * @return \Warkhosh\Variable\Variable
     */
    function post($var)
    {
        $var = \Warkhosh\Variable\Request::post((string)$var);
        $appVariable = new \Warkhosh\Variable\Variable($var);

        // Если передан второй параметр то значит это значения по умолчанию
        if (count(func_get_args()) >= 2) {
            $appVariable->byDefault(func_get_arg(1));
        }

        // Если передан третий параметр то значит это тип переменной по умолчанию
        if (count(func_get_args()) === 3) {
            $method = func_get_arg(2);
            $arguments = count(func_get_args()) >= 4 ? func_get_arg(3) : null;
            $appVariable->{$method}($arguments);

        } else {
            $appVariable->string();
        }

        return $appVariable;
    }
}

if (! function_exists('put')) {
    /**
     * @param string $var
     *
     * @return \Warkhosh\Variable\Variable
     */
    function put($var)
    {
        $var = \Warkhosh\Variable\Request::put((string)$var);
        $appVariable = new \Warkhosh\Variable\Variable($var);

        // Если передан второй параметр то значит это значения по умолчанию
        if (count(func_get_args()) >= 2) {
            $appVariable->byDefault(func_get_arg(1));
        }

        // Если передан третий параметр то значит это тип переменной по умолчанию
        if (count(func_get_args()) === 3) {
            $method = func_get_arg(2);
            $arguments = count(func_get_args()) >= 4 ? func_get_arg(3) : null;
            $appVariable->{$method}($arguments);

        } else {
            $appVariable->string();
        }

        return $appVariable;
    }
}

if (! function_exists('delete')) {
    /**
     * @param string $var
     *
     * @return \Warkhosh\Variable\Variable
     */
    function delete($var)
    {
        $var = \Warkhosh\Variable\Request::delete((string)$var);
        $appVariable = new \Warkhosh\Variable\Variable($var);

        // Если передан второй параметр то значит это значения по умолчанию
        if (count(func_get_args()) >= 2) {
            $appVariable->byDefault(func_get_arg(1));
        }

        // Если передан третий параметр то значит это тип переменной по умолчанию
        if (count(func_get_args()) === 3) {
            $method = func_get_arg(2);
            $arguments = count(func_get_args()) >= 4 ? func_get_arg(3) : null;
            $appVariable->{$method}($arguments);

        } else {
            $appVariable->string();
        }

        return $appVariable;
    }
}


if (! function_exists('request')) {
    /**
     * @param string $var
     *
     * @return \Warkhosh\Variable\Variable
     */
    function request($var)
    {
        $var = \Warkhosh\Variable\Request::any((string)$var);
        $appVariable = new \Warkhosh\Variable\Variable($var);

        // Если передан второй параметр то значит это значения по умолчанию
        if (count(func_get_args()) >= 2) {
            $appVariable->byDefault(func_get_arg(1));
        }

        // Еесли передан третий параметр то значит это тип переменной по умолчанию
        if (count(func_get_args()) === 3) {
            $method = func_get_arg(2);
            $appVariable->{$method}();
        } else {
            $appVariable->string();
        }

        return $appVariable;
    }
}
```