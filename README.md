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
     * @return \Warkhosh\Variable\Variable
     */
    function get(string $varName)
    {
        $value = \Warkhosh\Variable\Request::get($varName);
        $appVariable = new \Warkhosh\Variable\Variable($value);

        // Если передан второй параметр, то значит это значения по умолчанию
        if (count(func_get_args()) >= 2) {
            $appVariable->byDefault(func_get_arg(1));
        }

        // Если передан третий параметр, то значит это тип переменной по умолчанию
        if (count(func_get_args()) >= 3) {
            $method = func_get_arg(2);
            $arguments = count(func_get_args()) >= 4 ? func_get_arg(3) : null;
            $appVariable->{$method}($arguments);
        } else {
            if (is_array($var)) {
                trigger_error("Для чтения переменной `{$varName}`, указан не верный тип");
            }

            $appVariable->string();
        }

        return $appVariable;
    }
}


if (! function_exists('post')) {
    /**
     * @param string $varName
     * @return \Warkhosh\Variable\Variable
     */
    function post(string $varName)
    {
        $value = \Warkhosh\Variable\Request::post($varName);
        $appVariable = new \Warkhosh\Variable\Variable($value);

        // Если передан второй параметр, то значит это значения по умолчанию
        if (count(func_get_args()) >= 2) {
            $appVariable->byDefault(func_get_arg(1));
        }

        // Если передан третий параметр, то значит это тип переменной по умолчанию
        if (count(func_get_args()) === 3) {
            $method = func_get_arg(2);
            $arguments = count(func_get_args()) >= 4 ? func_get_arg(3) : null;
            $appVariable->{$method}($arguments);
        } else {
            if (is_array($value)) {
                trigger_error("Для чтения переменной `{$varName}`, указан не верный тип");
            }

            $appVariable->string();
        }

        return $appVariable;
    }
}

if (! function_exists('put')) {
    /**
     * @param string $varName
     * @return \Warkhosh\Variable\Variable
     */
    function put(string $varName)
    {
        $value = \Warkhosh\Variable\Request::put($varName);
        $appVariable = new \Warkhosh\Variable\Variable($value);

        // Если передан второй параметр, то значит это значения по умолчанию
        if (count(func_get_args()) >= 2) {
            $appVariable->byDefault(func_get_arg(1));
        }

        // Если передан третий параметр, то значит это тип переменной по умолчанию
        if (count(func_get_args()) === 3) {
            $method = func_get_arg(2);
            $arguments = count(func_get_args()) >= 4 ? func_get_arg(3) : null;
            $appVariable->{$method}($arguments);
        } else {
            if (is_array($value)) {
                trigger_error("Для чтения переменной `{$varName}`, указан не верный тип");
            }

            $appVariable->string();
        }

        return $appVariable;
    }
}

if (! function_exists('delete')) {
    /**
     * @param string $varName
     * @return \Warkhosh\Variable\Variable
     */
    function delete(string $varName)
    {
        $value = \Warkhosh\Variable\Request::delete($varName);
        $appVariable = new \Warkhosh\Variable\Variable($value);

        // Если передан второй параметр, то значит это значения по умолчанию
        if (count(func_get_args()) >= 2) {
            $appVariable->byDefault(func_get_arg(1));
        }

        // Если передан третий параметр, то значит это тип переменной по умолчанию
        if (count(func_get_args()) === 3) {
            $method = func_get_arg(2);
            $arguments = count(func_get_args()) >= 4 ? func_get_arg(3) : null;
            $appVariable->{$method}($arguments);
        } else {
            if (is_array($value)) {
                trigger_error("Для чтения переменной `{$varName}`, указан не верный тип");
            }

            $appVariable->string();
        }

        return $appVariable;
    }
}


if (! function_exists('request')) {
    /**
     * @param string $varName
     * @return \Warkhosh\Variable\Variable
     */
    function request(string $varName)
    {
        $value = \Warkhosh\Variable\Request::any($varName);
        $appVariable = new \Warkhosh\Variable\Variable($value);

        // Если передан второй параметр, то значит это значения по умолчанию
        if (count(func_get_args()) >= 2) {
            $appVariable->byDefault(func_get_arg(1));
        }

        // Если передан третий параметр, то значит это тип переменной по умолчанию
        if (count(func_get_args()) === 3) {
            $method = func_get_arg(2);
            $arguments = count(func_get_args()) >= 4 ? func_get_arg(3) : null;
            $appVariable->{$method}($arguments);
        } else {
            if (is_array($value)) {
                trigger_error("Для чтения переменной `{$varName}`, указан не верный тип");
            }

            $appVariable->string();
        }

        return $appVariable;
    }
}
```
