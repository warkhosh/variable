# Variable

Класс для автоматизации при работе с переменными по типам.

Классы с методами преобразование данных под конкретные типы:

- [VarArray](#VarArray)
- [VarFloat](#VarFloat)
- [VarStr](#VarStr)
- [VarInt](#VarInt)
- [VarBool](#VarBool)

Базовый пример:

```php
$value = [1, 2, 3, 4, 5];
$variable = new \Warkhosh\Variable\Variable($value, null);

# Получить все значения
var_dump($variable->get());

# Получить значение по ключу
echo $variable->get(1);
```

```php
$variable = new \Warkhosh\Variable\Variable("Konstantin");

# Получить значение Konstantin
echo $variable->get();
```

Примеры преобразования значения по алгоритмам:

```php
# Получить значение переменной в формате строки по алгоритму xs: trim + crop 50 символов
echo $variable->getInput("xs");

# Получить значение переменной в формате строки по алгоритму ids: в строке будут только числа через запятую больше нуля
echo $variable->getInput("ids");

# Получить значение переменной в формате строки по алгоритму tags: слова через запятую
echo $variable->getInput("tags");

# Получить значение переменной в формате строки по алгоритму small: trim + crop 255 символов
echo $variable->getInput("small");

# Получить значение переменной в формате строки без каких либо алгоритмов
echo $variable->getInput("unchanged");

# Получить значение переменной в формате строки которая содержит -1 или положительное число (включая ноль)
echo $variable->getInput("filter");

# Получить значение переменной в формате float c двумя числами после запятой
echo $variable->getFloat("cost");

# Получить значение переменной в формате int в которой ноль или единица
echo $variable->getInteger("toggle");

# Получить значение переменной в формате int в котором допускаются значения -1, 0, 1, 2, 3, 4...
echo $variable->getInteger("filter");

# Получить значение переменной в формате int в котором допускаются только положительное число
echo $variable->getInteger("option");

# Получить значение переменной в формате array в котором допускаются только значения положительных чисел больше нуля
echo $variable->getArray("ids");
```

> Для более гибкой настройки преобразования значений в классе есть методы input(), float(), integer(), array(), которые
применяют алгоритмы преобразования, но возвращают сам объект и таким образом можно запускать цепочку алгоритмов к
нужному для вас результату.

Пример преобразование значения в строку с лимитом в длину 255 символов и перевод в верхний регистр:

```php
echo $variable->input("small")->upper()->get();
```

Пример преобразования значения по правилу удаления не допустимых чисел меньше -1 и без нуля:

```php
echo $variable->input("filter")->removeZero()->get();
```

Пример проверки значения на допустимые значения:

```php
echo $variable->integer("filter")->inArray([-1, 0, 1, 2, 3, 4, 5])->get();
```


## VarArray

Класс для работы с переменными типа Array.

Примеры:
```php
echo \Warkhosh\Variable\VarArray::getMake('159, 555, age', ',');
echo \Warkhosh\Variable\VarArray::has("request.result", ['request' => ['result' => true]]);
echo \Warkhosh\Variable\VarArray::get("request.message", ['request' => ['message' => 'ok']]);
```

Со списком всех методов можно ознакомиться в Warkhosh\Variable\VarArray.php

## VarFloat

Класс для работы с переменными типа Float или Double.

Примеры:
```php
# Преобразование значения в число с плавающей точкой
echo \Warkhosh\Variable\VarFloat::getMake("159.127", 2, "upward");
```

Со списком всех методов можно ознакомиться в Warkhosh\Variable\VarFloat.php

## VarStr

Класс для работы с переменными типа String.

Примеры:
```php
echo \Warkhosh\Variable\VarStr::getMake(159);
echo \Warkhosh\Variable\VarStr::find("age", "My age is 18");
echo \Warkhosh\Variable\VarStr::start("/", "news/123.php");
echo \Warkhosh\Variable\VarStr::reduce("Какой хороший день, какой хороший пень", 15, "...");
```

Со списком всех методов можно ознакомиться в Warkhosh\Variable\VarStr.php

## VarInt

Класс для работы с переменными типа Integer.

Примеры:
```php
echo \Warkhosh\Variable\VarInt::getMake("159");
echo \Warkhosh\Variable\VarInt::isRange(3, 1, 5);
```

Со списком всех методов можно ознакомиться в Warkhosh\Variable\VarInt.php

## VarBool

Класс для работы с переменными типа Boolean.

Примеры:
```php
echo \Warkhosh\Variable\VarBool::getMake("0");
```

Со списком всех методов можно ознакомиться в Warkhosh\Variable\VarBool.php

## Дополнительные функции

```php
if (! function_exists('get')) {
    /**
     * @param string $varName
     * @param mixed $default
     * @return Variable
     * @package Warkhosh\Variable
     * @version 1.2
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
     * @version 1.2
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
     * @version 1.2
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
     * @version 1.2
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
     * @version 1.2
     */
    function request(string $varName, mixed $default)
    {
        $value = \Warkhosh\Variable\Request::any($varName);
        $appVariable = new \Warkhosh\Variable\Variable($value, $default);

        return $appVariable;
    }
}
```
