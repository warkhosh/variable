# Variable

### Help функции
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