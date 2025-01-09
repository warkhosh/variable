<?php

namespace Warkhosh\Variable;

use Exception;

/**
 * Class VarStr
 *
 * Класс по работе со строками
 *
 * @package Warkhosh\Variable
 */
class VarStr
{
    public const ENCODING = 'UTF-8';

    /**
     * Пробельные символы
     */
    public const SPACE_CHAR = [
        " ",        // пробел
        " ",     // No-break space = 0xC2,0xA0
        " ",     // En space       = 0xE2,0x80,0x82
        " ",     // Em space       = 0xE2,0x80,0x83
        " ",     // Thin space     = 0xE2,0x80,0x89
    ];

    /**
     * Пробельные коды
     */
    public const SPACE_CODE = [
        "&nbsp;",   // Мнемоник пробела
        "&emsp;",   // Мнемоник очень длинного пробел, примерно с длинное тире
        "&ensp;",   // Мнемоник длинного пробела, примерно с короткое тире
        "&thinsp;", // Мнемоник узкого пробела
    ];

    /**
     * Управляющие символы
     */
    public const CONTROL_CHAR = [
        "\n",       // Перевод каретки на следующую строку (0x0A)
        "\r",       // Перевод каретки в начало текущей строки (0x0D)
        "\t",       // Табуляция (tab)
        "\v",       // Вертикальная табуляция (0x0B)
    ];

    /**
     * Управляющие символы
     */
    public const SPECIAL_CHAR = [
        "\0",       // NUL-байт (0x00)
    ];

    /**
     * Преобразование переданного значения в текст
     *
     * @param mixed $str
     * @param string $default
     * @return string
     */
    public static function getMake(mixed $str = '', string $default = ''): string
    {
        return getMakeString($str, $default);
    }

    /**
     * Преобразование переданного значения в текст
     *
     * @param mixed $str
     * @return string
     * @deprecated заменить метод на VarStr::getMake
     */
    public static function getMakeString(mixed $str = ''): string
    {
        return getMakeString($str);
    }

    /**
     * Преобразование переданного значения в текст
     *
     * Это статичный метод алиас для getMake()
     *
     * @param mixed $str
     * @param string $default
     * @return void
     */
    public static function makeString(mixed &$str = '', string $default = ''): void
    {
        $str = getMakeString($str, $default);
    }

    /**
     * Определить, начинается ли указанная строка с определенной подстроки
     *
     * @param float|int|string|null $str
     * @param string|null $needle может быть массивом возможных подстрок
     * @return bool
     */
    public static function startsWith(float|int|string|null $str, ?string $needle = null): bool
    {
        if (is_null($needle)) {
            return false;
        }

        if (mb_strpos((string)$str, $needle) === 0) {
            return true;
        }

        return false;
    }

    /**
     * Определить, заканчивается ли указанная строка с определенной подстрокой
     *
     * @param float|int|string|null $str
     * @param string|null $needle может быть массивом возможных подстрок
     * @return bool
     */
    public static function endsWith(float|int|string|null $str, ?string $needle = null): bool
    {
        if (is_null($needle)) {
            return false;
        }

        if ($needle === mb_substr((string)$str, -mb_strlen($needle, self::ENCODING))) {
            return true;
        }

        return false;
    }

    /**
     * Регистрозависимый поиск вхождения текста в строке с возвратом номера позиции или false
     *
     * @note первый символ стоит на позиции 0, позиция второго 1 и так далее!
     *
     * @param string|null $needle строка, поиск которой производится в строке $str
     * @param float|int|string|null $str строка в которой ищем $needles
     * @param int $offset
     * @return false|int
     */
    public static function findPos(
        string|null $needle = null,
        float|int|string|null $str = '',
        int $offset = 0
    ): bool|int {
        if (is_null($needle)) {
            return false;
        }

        $pos = mb_strpos((string)$str, $needle, $offset, 'UTF-8');

        if ($pos !== false) {
            return $pos;
        }

        return false;
    }

    /**
     * Регистрозависимый поиск первого вхождения текста в строке с возвратом результата поиска
     *
     * @note Первый символ стоит на позиции 0, позиция второго 1 и так далее.
     * @param array|string|null $needles строка, поиск которой производится в строке $str
     * @param float|int|string|null $str строка в которой ищем $needles
     * @param int $offset
     * @return bool
     */
    public static function find(
        array|string|null $needles = null,
        float|int|string|null $str = '',
        int $offset = 0
    ): bool {
        if (is_null($needles)) {
            return false;
        }

        if (empty($needles)) {
            return false;
        }

        if (static::findPos($needles, $str, $offset) !== false) {
            return true;
        }

        return false;
    }

    /**
     * Замена первого вхождения строки
     *
     * @param string|null $search
     * @param string $replace
     * @param float|int|string|null $str
     * @return string
     */
    public static function replaceOnce(?string $search, string $replace, float|int|string|null $str = null): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        $str = (string)$str;

        if (mb_strlen($search) > 0) {
            $position = mb_strpos($str, (string)$search, 0, self::ENCODING);

            if ($position !== false) {
                $start = mb_substr($str, 0, $position, self::ENCODING);
                $endPosition = ($position + mb_strlen($search, self::ENCODING));
                $end = mb_substr($str, $endPosition, null, self::ENCODING);

                return $start.$replace.$end;
            }
        }

        return $str;
    }

    /**
     * Замена первой найденной строки в строке
     *
     * @param string $search
     * @param string $replace
     * @param float|int|string|null $str
     * @return string
     * @deprecated метод будет переименован в replaceOnce()
     */
    public static function str_replace_once(string $search, string $replace, float|int|string|null $str): string
    {
        return static::replaceOnce($search, $replace, (string)$str);
    }

    /**
     * Возвращает длину указанной строки
     *
     * @param float|int|string|null $value
     * @return int
     */
    public static function length(float|int|string|null $value): int
    {
        return mb_strlen((string)$value, self::ENCODING);
    }

    /**
     * Устанавливает начало строки на указанное с проверкой на наличие такого значения
     *
     * @param string $prefix
     * @param float|int|string|null $str
     * @return string
     */
    public static function start(string $prefix, float|int|string|null $str): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        if (mb_strlen($prefix) > 0) {
            $quoted = preg_quote($prefix, '/');

            return $prefix.preg_replace('/^(?:'.$quoted.')+/u', '', (string)$str);
        }

        return (string)$str;
    }

    /**
     * Завершает строку заданным значением с проверкой на наличие такого значения
     *
     * @param string $postfix
     * @param float|int|string|null $str
     * @return string
     */
    public static function ending(string $postfix, float|int|string|null $str): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        if (mb_strlen($postfix) > 0) {
            $quoted = preg_quote($postfix, '/');

            return preg_replace('/(?:'.$quoted.')+$/u', '', (string)$str).$postfix;
        }

        return (string)$str;
    }

    /**
     * Убирает указное значения из начала строки
     *
     * @param string $prefix
     * @param float|int|string|null $str
     * @return string
     */
    public static function getRemoveStart(string $prefix, float|int|string|null $str): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        if (mb_strlen($prefix) > 0) {
            $quoted = preg_quote($prefix, '/');

            return (string)preg_replace('/^(?:'.$quoted.')+/u', '', (string)$str);
        }

        return (string)$str;
    }

    /**
     * Убирает указную строку из конца строки
     *
     * @param string $postfix
     * @param float|int|string|null $str
     * @return string
     */
    public static function getRemoveEnding(string $postfix, float|int|string|null $str): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        if (mb_strlen($postfix) > 0) {
            $quoted = preg_quote($postfix, '/');

            return (string)preg_replace('/(?:'.$quoted.')+$/u', '', (string)$str);
        }

        return (string)$str;
    }

    /**
     * Убирает указную строку из начала строки
     *
     * @param string $search
     * @param string $replace
     * @param float|int|string|null $str
     * @return string
     */
    public static function getReplaceStart(string $search, string $replace, float|int|string|null $str): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        if (mb_strlen($search) > 0) {
            $quoted = preg_quote($search, '/');

            return (string)preg_replace('/^(?:'.$quoted.')+/u', $replace, (string)$str);
        }

        return (string)$str;
    }

    /**
     * Убирает указную строку из конца строки
     *
     * @param string $search
     * @param string $replace
     * @param float|int|string|null $str
     * @return string
     */
    public static function getReplaceEnding(string $search, string $replace, float|int|string|null $str): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        if (mb_strlen($search) > 0) {
            $quoted = preg_quote($search, '/');

            return (string)preg_replace('/(?:'.$quoted.')+$/u', $replace, (string)$str);
        }

        return (string)$str;
    }

    /**
     * Кодирует данные в формат MIME base64
     *
     * @param float|int|string|null $str
     * @return string
     */
    public static function getBase64UrlEncode(float|int|string|null $str): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        return rtrim(strtr(base64_encode((string)$str), '+/', '-_'), '=');
    }

    /**
     * Декодирует данные, закодированные MIME base64
     *
     * @param float|int|string|null $str
     * @return string
     */
    public static function getBase64UrlDecode(float|int|string|null $str): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        $string = strtr((string)$str, '-_', '+/');
        $length = mb_strlen((string)$str, self::ENCODING) % 4;

        return base64_decode(str_pad($string, $length, '=', STR_PAD_RIGHT));
    }

    /**
     * Преобразовать данную строку в нижний регистр (lower-case)
     *
     * @note нужно учитывать что чем больше символов строке тем больше потребуется память при preg_split()!
     *
     * @param float|int|string|null $words
     * @return string
     * @throws Exception
     */
    public static function getLower(float|int|string|null $words): string
    {
        if (is_null($words) || (is_string($words) && trim($words) === '')) {
            return '';
        }

        if (is_numeric($words)) {
            return (string)$words;
        }

        $replaceChar = [
            "А" => "а",
            "Б" => "б",
            "В" => "в",
            "Г" => "г",
            "Д" => "д",
            "Е" => "е",
            "Ё" => "ё",
            "Ж" => "ж",
            "З" => "з",
            "И" => "и",
            "Й" => "й",
            "К" => "к",
            "Л" => "л",
            "М" => "м",
            "Н" => "н",
            "О" => "о",
            "П" => "п",
            "Р" => "р",
            "С" => "с",
            "Т" => "т",
            "У" => "у",
            "Ф" => "ф",
            "Х" => "х",
            "Ц" => "ц",
            "Ч" => "ч",
            "Ш" => "ш",
            "Щ" => "щ",
            "Ъ" => "ъ",
            "Ы" => "ы",
            "Ь" => "ь",
            "Э" => "э",
            "Ю" => "ю",
            "Я" => "я",
        ];

        $str = "";

        // специально сохраняем символ плюса
        $words = str_replace('+', '[=FIX_CHAR_PLUS_REPLACE=]', $words);
        $words = urldecode($words);
        $words = str_replace('[=FIX_CHAR_PLUS_REPLACE=]', '+', $words);

        $words = VarStr::toUTF8($words);
        $words = mb_strtolower($words); // другие символы в нижний регистр
        $words = preg_split('//u', $words, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($words as $row) {
            $str .= $replaceChar[$row] ?? $row;
        }

        return $str;
    }

    /**
     * Преобразовать данную строку в верхний регистр (upper-case)
     *
     * @note нужно учитывать что чем больше символов строке тем больше потребуется память при preg_split()!
     *
     * @param float|int|string|null $words
     * @return string
     * @throws Exception
     */
    public static function getUpper(float|int|string|null $words): string
    {
        if (is_null($words) || (is_string($words) && trim($words) === '')) {
            return '';
        }

        if (is_numeric($words)) {
            return (string)$words;
        }

        $replaceChar = [
            "а" => "А",
            "б" => "Б",
            "в" => "В",
            "г" => "Г",
            "д" => "Д",
            "е" => "Е",
            "ё" => "Ё",
            "ж" => "Ж",
            "з" => "З",
            "и" => "И",
            "й" => "Й",
            "к" => "К",
            "л" => "Л",
            "м" => "М",
            "н" => "Н",
            "о" => "О",
            "п" => "П",
            "р" => "Р",
            "с" => "С",
            "т" => "Т",
            "у" => "У",
            "ф" => "Ф",
            "х" => "Х",
            "ц" => "Ц",
            "ч" => "Ч",
            "ш" => "Ш",
            "щ" => "Щ",
            "ъ" => "Ъ",
            "ы" => "Ы",
            "ь" => "Ь",
            "э" => "Э",
            "ю" => "Ю",
            "я" => "Я",
        ];

        $str = "";

        // специально сохраняем символ плюса
        $words = str_replace('+', '[=FIX_CHAR_PLUS_REPLACE=]', $words);
        $words = urldecode($words);
        $words = str_replace('[=FIX_CHAR_PLUS_REPLACE=]', '+', $words);

        $words = VarStr::toUTF8($words);
        $words = mb_strtoupper($words, 'UTF-8'); // другие символы в верхний регистр
        $words = preg_split('//u', $words, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($words as $row) {
            $str .= $replaceChar[$row] ?? $row;
        }

        return $str;
    }

    /**
     * Ограничить количество слов в строке
     *
     * @note предназначен для работы с простыми кодировками
     *
     * @param float|int|string|null $str
     * @param int $words
     * @param string $end
     * @return string
     */
    public static function words(float|int|string|null $str, int $words = 100, string $end = '...'): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        $str = (string)$str;

        if ($words >= 0) {
            preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $str, $matches);

            if (! isset($matches[0])
                || mb_strlen($str, self::ENCODING) === mb_strlen($matches[0], self::ENCODING)) {
                return $str;
            }

            return rtrim($matches[0]).$end;
        }

        return $str;
    }

    /**
     * Обрезает строку до указанных символов
     *
     * @note метод использует trim() перед обрезанием
     *
     * @param float|int|string|null $str
     * @param int $length
     * @return string
     */
    public static function crop(float|int|string|null $str, int $length = 250): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        if ($length !== 0) {
            $str = VarStr::trim((string)$str);

            return mb_substr($str, 0, $length, self::ENCODING);
        }

        return "";
    }

    /**
     * Сокращает текст по параметрам
     *
     * @note метод использует trim() перед сокращением
     *
     * @param float|int|string|null $str
     * @param int $length
     * @param string $ending
     * @param bool $transform преобразование кодов в символы и обратно (дял подсчета длинны по символам)
     * @param bool $smart флаг включающий умную систему усечения строки с учётом целостности слов
     * @return string
     */
    public static function reduce(
        float|int|string|null $str,
        int $length = 250,
        string $ending = '',
        bool $transform = true,
        bool $smart = true
    ): string {
        if ($length <= 0 || is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        $str = VarStr::trim((string)$str);

        // Вычисляем длину текста с учетом количества символов от переменной $ending
        $maxLength = $length - mb_strlen($ending, self::ENCODING);

        // Переустанавливаем значение окончания через проверку длинны текста
        $ending = mb_strlen($str, self::ENCODING) > $maxLength ? $ending : '';

        // Защита от человеческого фактора когда длинна строки меньше чем окончание
        if ($maxLength < 1) {
            return "";
        }

        // Кодирует коды HTML-сущностей в символы для более точного усечения
        if ($transform) {
            $str = static::getHtmlEntityDecode($str);
        }

        // Жёсткое обрезание текста, строго по лимиту
        if ($smart !== true) {
            $returnStr = VarStr::crop($str, $length);
            $returnStr .= $returnStr !== $str ? $ending : "";

            // Кодирует символы в HTML-сущности если указали флаг преобразования
            return $transform ? static::getHtmlSpecialCharsEncode($returnStr) : $returnStr;
        }

        // Длинна строки больше чем требуется
        if (mb_strlen($str, self::ENCODING) > $length) {
            // поиск пробелов в тексте
            if (mb_strstr($str, ' ') === false) {
                // Укорачиваем единственное слово по точному количеству символов раз в строке нет пробелов
                $str = mb_substr($str, 0, $maxLength, self::ENCODING);

            } else {
                $words = [];

                foreach (explode(" ", $str) as $string) {
                    if (mb_strlen(join(" ", $words), self::ENCODING) < $maxLength) {
                        $words[] = $string;
                    }
                }

                // страховка на случай если первое и единственное выбранное слово превышает указанную длину
                if (count($words) === 1) {
                    $str = mb_substr($words[0], 0, $maxLength, self::ENCODING);

                } else {
                    array_pop($words); // убираем последнее слово делающее превышение ограничения по длине
                    $str = static::trim(join(" ", $words));
                }
            }

            $str .= $ending;
        }

        if ($transform) {
            $str = static::getHtmlSpecialCharsEncode($str); // Кодирует символы в HTML-сущности
        }

        return $str;
    }

    /**
     * Convert a value to studly caps case
     *
     * @param float|int|string|null $str
     * @return string
     */
    public static function getStudly(float|int|string|null $str): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        $str = ucwords(str_replace(['-', '_'], ' ', (string)$str));

        return str_replace(' ', '', $str);
    }

    /**
     * Преобразование Camel case в Snake case
     *
     * @param float|int|string|null $str
     * @return string
     */
    public static function getSnakeCase(float|int|string|null $str): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', (string)$str, $matches);
        $ret = (array)$matches[0];

        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }

    /**
     * Преобразование Snake case в Camel case
     *
     * @param float|int|string|null $str
     * @return string
     * @throws Exception
     */
    public static function getCamelCase(float|int|string|null $str): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        return join("", VarArray::ucfirst(explode("_", (string)$str)));
    }

    /**
     * Преобразует HTML-сущности (Entity) в специальные символы
     *
     * @example: &amp;copy; > &copy; | &amp; > & | &quot; > " | &bull; > •
     *
     * @param float|int|string|null $str
     * @param int $flags битовая маска из флагов определяющая режим обработки
     * @param string $encoding кодировка
     * @return string
     */
    public static function getHtmlEntityDecode(
        float|int|string|null $str,
        int $flags = ENT_COMPAT | ENT_HTML5,
        string $encoding = 'UTF-8'
    ): string {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        return html_entity_decode((string)$str, $flags, $encoding);
    }

    /**
     * Кодирует только специальные символы в их HTML-сущности
     *
     * @note Кодирует только символы &, ", ', <, >, для кодирования всех символов используйте self::htmlEntityEncode()
     * @example & > &amp; | " > &quot; | ' > &apos; | > в &lt; | < в &gt;
     *
     * @param float|int|string|null $str
     * @param int $flags битовая маска из флагов определяющая режим обработки
     * @param string $encoding кодировка
     * @param bool $doubleEncode при выключении не будет преобразовывать существующие HTML-сущности. При включении приведет к преобразованию &apos; > &amp;&apos;
     * @return string
     */
    public static function getHtmlSpecialCharsEncode(
        float|int|string|null $str,
        int $flags = ENT_COMPAT | ENT_HTML5,
        string $encoding = 'UTF-8',
        bool $doubleEncode = false
    ): string {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        return htmlspecialchars((string)$str, $flags, $encoding, $doubleEncode);
    }

    /**
     * Кодирует (все допустимые) символы в соответствующие HTML-сущности (Entity)
     * Если надо преобразовать &copy; > &amp;copy; следует четвертый параметр $htmlEncode установить в TRUE
     *
     * @note для преобразования только символов &, ", ', <, > используйте self::getHtmlSpecialCharsEncode()
     * @example & > &amp; | " > &quot;
     *
     * @param float|int|string|null $str
     * @param int $flags битовая маска из флагов определяющая режим обработки
     * @param string $encoding кодировка
     * @param bool $doubleEncode при выключении не будет преобразовывать существующие HTML-сущности. При включении приведет к преобразованию &copy; > &amp;copy;
     * @return string
     */
    public static function getHtmlEntityEncode(
        float|int|string|null $str,
        int $flags = ENT_COMPAT | ENT_HTML5,
        string $encoding = 'UTF-8',
        bool $doubleEncode = false
    ): string {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        return htmlentities((string)$str, $flags, $encoding, $doubleEncode);
    }

    /**
     * Декодирование закодированной URL строки
     *
     * @param float|int|string|null $str строка, которая должна быть декодирована
     * @param bool $raw флаг для переключения метода декодирования на rawurldecode() без преобразования символа +
     * @param string $encoding кодировка
     * @return string
     * @throws Exception
     */
    public static function getUrlDecode(
        float|int|string|null $str = '',
        bool $raw = false,
        string $encoding = 'UTF-8'
    ): string {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        $str = static::getTransformToEncoding((string)$str, $encoding);

        if ($raw) {
            return rawurldecode($str); // раскодирует контент по RFC 3986
        }

        // стараемся сохранить символ плюса
        $str = str_replace('+', '[=FIX_CHAR_PLUS_REPLACE=]', $str);
        $str = urldecode($str); // раскодирует контент по типу application/x-www-form-urlencoded где пробел это +

        return str_replace('[=FIX_CHAR_PLUS_REPLACE=]', '+', $str);
    }

    /**
     * Кодирование строки для URL
     *
     * @note RFC 3986: строка, в которой все не цифро-буквенные символы, кроме -_.~,
     *       должны быть заменены знаком процента (%) за которым следует два шестнадцатеричных числа
     *
     * @param float|int|string|null $str строка, которая должна быть декодирована.
     * @param bool $raw флаг для переключения метода кодирования на rawurlencode() согласно RFC 3986 без преобразования символа +
     * @param string $encoding кодировка
     * @return string
     * @throws Exception
     */
    public static function getUrlEncode(
        float|int|string|null $str = '',
        bool $raw = false,
        string $encoding = 'UTF-8'
    ): string {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        $str = static::getTransformToEncoding((string)$str, $encoding);

        if ($raw) {
            return rawurlencode($str); // кодирует строку по RFC 3986
        }

        // Возвращает строку, в которой все не цифро-буквенные символы, кроме (-_.) должны быть заменены знаком процента (%),
        // за которым следует два шестнадцатеричных числа, а пробелы закодированы как знак сложения (+).
        // Строка кодируется тем же способом, что и POST-данные веб-формы, то есть по типу контента application/x-www-form-urlencoded
        return urlencode($str);
    }

    /**
     * Форматирует переданное значение с разделением групп
     *
     * @note этот метод преобразовывает строку, а в Variable::getNumberFormat() рассчитан да объект Variable который может быть массивом!
     *
     * @param float|int|string|null $str
     * @param int $decimals точность (символы после точки)
     * @param string $separator разделитель точности
     * @param string $thousands_sep разделитель тысяч
     * @param float|int $default
     * @return string
     * @throws Exception
     */
    public static function getNumberFormat(
        float|int|string|null $str,
        int $decimals = 2,
        string $separator = '.',
        string $thousands_sep = '',
        float|int $default = 0
    ): string {
        $decimals = $decimals >= 0 ? $decimals : 2;

        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return number_format((float)$default, $decimals, $separator, $thousands_sep);
        }

        $price = preg_replace('/[^0-9,.\s]/ium', '', (string)$str);

        if ((string)$str !== $price) {
            throw new Exception("Incorrect number");
        }

        $str = static::trim((string)$str);
        $dotParts = explode(".", $str);
        $commaParts = explode(",", $str);
        $spaceParts = explode(" ", $str);

        $fidDot = count($dotParts) > 1;
        $fidComma = count($commaParts) > 1;
        $fidSpace = count($spaceParts) > 1;

        // Проверка наличия только одного разделителя в тексте
        if (((int)$fidDot + (int)$fidComma + (int)$fidSpace) > 1) {
            throw new Exception("There are several separators in value of number");
        }

        // Проверка повторяющегося разделителя
        if (($fidDot + $fidComma + $fidSpace) > 1) {
            throw new Exception("Invalid number format");
        }

        $str = VarFloat::getMake($str, $decimals, $default);

        return number_format($str, $decimals, $separator, $thousands_sep);
    }

    /**
     * Создает токен по двум алгоритмам
     *
     * @note
     * @param int $length длинна возвращаемых символов (без учёта $split)
     * @param int $split количество символов для разделения строки на части через символ тире
     * @param bool $readable флаг для генерации более простых кодов
     * @return string
     * @throws Exception
     */
    public static function getRandomToken(int $length = 128, int $split = 0, bool $readable = false): string
    {
        if (! ($length > 0)) {
            throw new Exception("Specify the length to generate the string");
        }

        $unreadablePool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $readablePool = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $times = (int)VarFloat::getRound($length / 10, 0, 'upward');
        $pool = $readable ? $readablePool : $unreadablePool;

        $value = substr(str_shuffle(str_repeat($pool, $times)), 0, $length);

        if ($split > 0) {
            $value = join('-', str_split($value, $split));
        }

        return $value;
    }

    /**
     * Создает токен по двум алгоритмам
     *
     * @param int $length длинна возвращаемых символов (без учёта $split)
     * @param int $split количество символов для разделения строки на части через символ тире
     * @param bool $readable флаг для генерации более простых кодов
     * @return string
     * @throws Exception
     * @deprecated изменилось название метода getRandomToken()
     */
    public static function randomToken(int $length = 128, int $split = 0, bool $readable = false): string
    {
        return static::getRandomToken($length, $split, $readable);
    }

    /**
     * Создает цифирный код
     *
     * @param int $length длинна возвращаемых символов (без учёта $split)
     * @param int $split количество символов для разделения строки на части через символ тире
     * @return string
     * @throws Exception
     */
    public static function getRandomNumberCode(int $length = 6, int $split = 0): string
    {
        if (! ($length > 0)) {
            throw new Exception("Specify the length to generate the string");
        }

        $pool = '0123456789';
        $times = (int)VarFloat::getRound($length / 10, 0, 'upward');
        $str = substr(str_shuffle(str_repeat($pool, $times)), 0, $length);

        if ($split > 0) {
            $str = join('-', str_split($str, $split));
        }

        return $str;
    }

    /**
     * Создает цифирный код
     *
     * @param int $length длинна возвращаемых символов (без учёта $split)
     * @param int $split количество символов для разделения строки на части через символ тире
     * @return string
     * @throws Exception
     * @deprecated изменилось название метода getRandomNumberCode()
     */
    public static function randomNumberCode(int $length = 6, int $split = 0): string
    {
        return static::getRandomNumberCode($length, $split);
    }

    /**
     * Возвращает часть строки
     *
     * @param float|int|string|null $str
     * @param int $start
     * @param int|null $length
     * @return string
     */
    public static function substr(float|int|string|null $str, int $start, ?int $length = null): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        return mb_substr($str, $start, $length, 'UTF-8');
    }

    /**
     * Преобразует первый символ строки в верхний регистр
     *
     * @param float|int|string|null $str
     * @return string
     * @throws Exception
     */
    public static function ucfirst(float|int|string|null $str): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        $str = getMakeString($str);
        $char = mb_substr($str, 0, 1, 'UTF-8');

        return static::getUpper($char).mb_substr($str, 1);
    }

    /**
     * Преобразует последний символ строки в верхний регистр
     *
     * @param float|int|string|null $str
     * @return string
     * @throws Exception
     */
    public static function ucLast(float|int|string|null $str): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        $str = (string)$str;
        $char = mb_substr($str, -1, 1, 'UTF-8');

        return mb_substr($str, 0, mb_strlen($str) - 1, 'UTF-8').static::getUpper($char);
    }

    /**
     * Убирает из строки разные пробелы и технические символы
     *
     * @note промежуточный метод для последующих проверок на пустоту в другим местах
     *
     * @param string|null $str
     * @return string
     */
    public static function getClean(?string $str): string
    {
        if (is_null($str) || (trim($str) === '')) {
            return '';
        }

        //$search = ["\n", "\t", "\r", '&nbsp;', '&emsp;', '&ensp;', '&thinsp;'];
        $search = array_merge(static::SPACE_CHAR, static::SPACE_CODE, static::CONTROL_CHAR);
        $str = str_replace($search, ' ', $str);

        return static::trim(preg_replace('/\s{2,}/', ' ', $str), "\x00..\x1F"); // убрать лишние пробелы
    }

    /**
     * Проверка строки на пустое значение
     *
     * @param float|int|string|null $str
     * @param bool $strict
     * @return bool
     */
    public static function isEmpty(float|int|string|null $str, bool $strict = true): bool
    {
        return isEmptyString($str, $strict);
    }

    /**
     * Удаляет пробелы из начала и конца строки (или другие символы при передачах их вторым параметром)
     *
     * @param float|int|string|null $str
     * @param string $remove список символов для удаления
     * @return string
     */
    public static function trim(float|int|string|null $str, string $remove = " \t\n\r\v\0\x0B"): string
    {
        return getTrimString($str, $remove);
    }

    /**
     * Замена повторяющихся символов из списка $char в значения из списка $replace
     *
     * @note нужно учитывать что список $replace должен совпадать по длине с $char
     *
     * @param float|int|string|null $str
     * @param array|string $char
     * @param array|string $replace
     * @return string
     */
    public static function getRemovingDoubleChar(
        float|int|string|null $str = '',
        array|string $char = [' '],
        array|string $replace = [' ']
    ): string {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        $char = is_array($char) ? $char : [$char];
        $replace = is_array($replace) ? $replace : [$replace];

        foreach ($char as $key => $symbol) {
            if (isset($replace[$key])) {
                $quoted = preg_quote($symbol, '/');
                $str = static::trim(preg_replace("/".$quoted."{2,}/", $replace[$key], (string)$str));
            }
        }

        return (string)$str;
    }

    /**
     * Удаление указанных символов из строки
     *
     * @param float|int|string|null $str
     * @param array|string $removeChar список символов для удаления
     * @return string
     */
    public static function getRemoveSymbol(
        float|int|string|null $str,
        array|string $removeChar = ["\n", "\r", "\t", "\v"]
    ): string {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        $removeChar = is_array($removeChar) ? $removeChar : [$removeChar];

        if (count($removeChar) > 0) {
            $str = str_replace($removeChar, '', (string)$str);
        }

        return (string)$str;
    }

    /**
     * Проверка даты под указанный формат
     *
     * @param float|int|string|null $str
     * @param string $format
     * @return bool
     * @deprecated методы с датой теперь в VarDateTime!
     */
    public static function validateDateTime(float|int|string|null $str = null, string $format = 'Y-m-d H:i:s'): bool
    {
        return VarDateTime::validateDateTime($str, $format);
    }

    /**
     * Преобразует строку в дату по указанному формату
     *
     * @param string|null $str
     * @param string $format
     * @param string|null $default
     * @return void
     * @throws Exception
     * @deprecated методы с датой теперь в VarDateTime!
     */
    public static function makeDate(?string &$str, string $format = 'Y-m-d', ?string $default = null): void
    {
        VarDateTime::makeDateTime($str, $format, $default);
    }

    /**
     * Возвращает название кодировки у переданной строки
     *
     * @param float|int|string|null $str
     * @return string|null
     */
    public static function getEncoding(float|int|string|null $str): ?string
    {
        $currentEncoding = mb_detect_encoding((string)$str, mb_detect_order(), false);

        $encoding = match ($currentEncoding) {
            'ASCII' => mb_convert_encoding((string)$str, 'ASCII'),
            'Windows-1251' => mb_convert_encoding((string)$str, 'Windows-1251'),
            default => mb_convert_encoding((string)$str, 'UTF-8'),
        };

        if ($encoding !== false) {
            return $currentEncoding;
        }

        return null;
    }

    /**
     * Безопасное преобразование строки в utf-8
     *
     * @note в случае ошибок эта функция не бросает исключения, для этого используйте метод getTransformToEncoding()
     *
     * @param float|int|string|null $str
     * @return string
     */
    public static function toUTF8(float|int|string|null $str = ''): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        $str = (string)$str;

        // Если кодировка строки отличается от указанной
        if (! mb_check_encoding($str, "UTF-8")) {
            $encoding = mb_detect_encoding($str, mb_detect_order(), false);

            // Кодировку текущей строки не возможно определить
            if (is_bool($encoding)) {
                return @iconv("UTF-8", "UTF-8//IGNORE", $str);
            }

            if ($encoding === "UTF-8") {
                return $str;
            }

            $convertedStr = mb_convert_encoding($str, 'UTF-8', $encoding);

            // Проверка некорректной работы mb_convert_encoding()
            return $convertedStr === false
                ? (string)@iconv($encoding, "UTF-8//IGNORE", $str)
                : $convertedStr;
        }

        return $str;
    }

    /**
     * Преобразование строки в указанную кодировку если она таковой не является
     *
     * @param float|int|string|null $str строка, для которой требуется определить кодировку
     * @param string $encoding ожидаемая кодировка
     * @return string
     * @throws Exception
     */
    public static function getTransformToEncoding(float|int|string|null $str = '', string $encoding = 'UTF-8'): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        if ($encoding === 'UTF-8') {
            return static::toUTF8($str);
        }

        $str = (string)$str;

        // Если кодировки строки отличается от указанной
        if (! mb_check_encoding($str, $encoding)) {
            // Определение кодировки у указанной строки
            $currentEncoding = mb_detect_encoding($str, mb_detect_order(), false);

            // Кодировку текущей строки не возможно определить
            if (is_bool($currentEncoding)) {
                throw new Exception("Unable to determine character encoding for the specified string");
            }

            // Преобразование строки из одной кодировки символов в другую
            $text = mb_convert_encoding($str, $encoding);

            // Проверка некорректной работы mb_convert_encoding()
            if ($text === false) {
                throw new Exception("Error when trying to convert a string via mb_convert_encoding()");
            }

            return (string)iconv($currentEncoding, "{$encoding}//IGNORE", $text);
        }

        return $str;
    }

    /**
     * Разбивает строку по разделителю и дополнительно производит удаление пустых значений
     *
     * @param string $delimiter разделитель
     * @param float|int|string|null $str строка
     * @param array|null $deleted массив значений которые надо удалить
     * @return array
     */
    public static function explode(
        string $delimiter,
        float|int|string|null $str,
        ?array $deleted = ['', 0, null]
    ): array {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return [];
        }

        $str = static::trim((string)$str);

        if (is_array($deleted) && count($deleted) > 0) {
            $parts = explode($delimiter, $str);

            return VarArray::getRemove($parts, $deleted);
        }

        return explode($delimiter, $str);
    }

    /**
     * Разбивает строку с числами по разделителю и дополнительно производит удаление недопустимых значений алгоритма $action
     *
     * @param string $delimiter разделитель
     * @param float|int|string|null $str строка
     * @param string $action (id|ids|number|int|integer)
     * @return array
     * @throws Exception
     */
    public static function explodeToNumber(string $delimiter, float|int|string|null $str, string $action = "ids"): array
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return [];
        }

        $idList = explode($delimiter, static::trim((string)$str));

        switch ($action) {
            case 'ids':
            case 'id':
                $idList = array_map('intval', $idList);

                foreach ($idList as $key => $id) {
                    if ($id <= 0) {
                        unset($idList[$key]);
                    }
                }

                $idList = array_unique($idList, SORT_NUMERIC);
                $idList = array_values($idList);
                break;

            case 'integer':
            case 'int':
                foreach ($idList as $key => $id) {
                    $int = intval($id);

                    if ((string)$id !== (string)$int) {
                        unset($idList[$key]);
                        continue;
                    }

                    $idList[$key] = $int;
                }

                $idList = array_values($idList);
                break;

            case 'number':
                foreach ($idList as $key => $id) {
                    if (! is_numeric($id)) {
                        unset($idList[$key]);
                        continue;
                    }

                    if (is_float($id + 0)) {
                        $idList[$key] = VarFloat::getMake($id);
                    } else {
                        $idList[$key] = VarInt::getMake($id);
                    }
                }

                break;
        }

        return $idList;
    }

    /**
     * Преобразует HTML-сущности в специальные символы через регулярное выражение и список сущностей.
     * Преобразует &amp; > & | &quot; > " | &bull; > •
     *
     * @param float|int|string|null $str
     * @param int $flags
     * @param string $charset = utf-8 (ISO-8859-1)
     * @return string
     * @todo это решение на php4, но пока решил оставить на всякий случай
     */
    public static function getDecodeEntities(
        float|int|string|null $str,
        int $flags = ENT_COMPAT,
        string $charset = 'UTF-8'
    ): string {
        $str = preg_replace_callback('/&([a-zA-Z][a-zA-Z0-9]+);/', 'convert_entity', (string)$str);

        return html_entity_decode((string)$str, $flags, $charset);
    }

    /**
     * Транскрипция текста
     *
     * @param float|int|string|null $str
     * @param bool $lower
     * @param array $ignoreChars
     * @return string
     */
    public static function getTranscription(
        float|int|string|null $str,
        bool $lower = true,
        array $ignoreChars = []
    ): string {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        $str = static::getTransformToEncoding($str);
        $str = strip_tags(static::trim($str));
        $str = static::getDecodeEntities($str);
        $str = urldecode($str); // Декодирование URL из кодированной строки

        $char = [
            // lower
            "а" => "a",
            "б" => "b",
            "в" => "v",
            "г" => "g",
            "д" => "d",
            "е" => "e",
            "ё" => "yo",
            "ж" => "zh",
            "з" => "z",
            "и" => "i",
            "й" => "y",
            "к" => "k",
            "л" => "l",
            "м" => "m",
            "н" => "n",
            "о" => "o",
            "п" => "p",
            "р" => "r",
            "с" => "s",
            "т" => "t",
            "у" => "u",
            "ф" => "f",
            "х" => "kh",
            "ц" => "c",
            "ч" => "ch",
            "ш" => "sh",
            "щ" => "sch",
            "ъ" => "",
            "ы" => "y",
            "ь" => "",
            "э" => "e",
            "ю" => "yu",
            "я" => "ya",
            // upper
            "А" => "a",
            "Б" => "b",
            "В" => "v",
            "Г" => "g",
            "Д" => "d",
            "Е" => "e",
            "Ё" => "yo",
            "Ж" => "zh",
            "З" => "z",
            "И" => "i",
            "Й" => "y",
            "К" => "k",
            "Л" => "l",
            "М" => "m",
            "Н" => "n",
            "О" => "o",
            "П" => "p",
            "Р" => "r",
            "С" => "s",
            "Т" => "t",
            "У" => "u",
            "Ф" => "f",
            "Х" => "kh",
            "Ц" => "c",
            "Ч" => "ch",
            "Ш" => "sh",
            "Щ" => "sch",
            "Ъ" => "",
            "Ы" => "y",
            "Ь" => "",
            "Э" => "e",
            "Ю" => "yu",
            "Я" => "ya",
            // over
            "-" => "-",
            " " => "-",
            "_" => "_",
            "\\" => "-diff-",
            "/" => "-slash-",
            "+" => "-plus-",
            "=" => "-equal-",
            ":" => "-colon-",
            "." => "-dot-",
            "\"" => "-quote-",
            "#" => "-num-",
            "%" => "-percnt-",
            "&" => "-amp-",
            "$" => "-dollar-",
            "€" => "-euro-",
            "₽" => "-ruble-",
            "?" => "-quest-",
            "!" => "-excl-",
            "@" => "-commat-",
            "*" => "-asterisk-",
            "©" => "-copy-",
            "®" => "-reg-",
            // number
            "0" => "0",
            "1" => "1",
            "2" => "2",
            "3" => "3",
            "4" => "4",
            "5" => "5",
            "6" => "6",
            "7" => "7",
            "8" => "8",
            "9" => "9",
        ];

        if ($lower) {
            $str = static::getLower($str);
        }

        $result = '';
        $words = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);

        if (is_array($words) && count($words)) {
            foreach ($words as $value) {
                if (in_array($value, $ignoreChars)) {
                    $result .= $value;
                    continue;
                }

                // преобразовывает символы в их аналоги
                $result .= isset($char[$value]) && array_key_exists($value, $char) ? $char[$value] : $value;
            }
        }

        return preg_replace("/[-]+/", '-', $result); // убираем дубли
    }

    /**
     * Преобразует строку к виду slug
     *
     * @note подходит для использования в урлах
     *
     * @param float|int|string|null $str
     * @param bool $lower
     * @param array $ignoreChars
     * @return string
     * @throws Exception
     */
    public static function getSlug(float|int|string|null $str, bool $lower = true, array $ignoreChars = []): string
    {
        if (is_null($str) || (is_string($str) && trim($str) === '')) {
            return '';
        }

        $str = static::getTransformToEncoding($str);
        $str = strip_tags(static::trim($str));
        $str = static::getDecodeEntities($str);
        $str = urldecode($str); // Декодирование URL из кодированной строки

        $char = [
            // lower
            "а" => "a",
            "б" => "b",
            "в" => "v",
            "г" => "g",
            "д" => "d",
            "е" => "e",
            "ё" => "yo",
            "ж" => "zh",
            "з" => "z",
            "и" => "i",
            "й" => "y",
            "к" => "k",
            "л" => "l",
            "м" => "m",
            "н" => "n",
            "о" => "o",
            "п" => "p",
            "р" => "r",
            "с" => "s",
            "т" => "t",
            "у" => "u",
            "ф" => "f",
            "х" => "kh",
            "ц" => "c",
            "ч" => "ch",
            "ш" => "sh",
            "щ" => "sch",
            "ъ" => "",
            "ы" => "y",
            "ь" => "",
            "э" => "e",
            "ю" => "yu",
            "я" => "ya",
            // upper
            "А" => "a",
            "Б" => "b",
            "В" => "v",
            "Г" => "g",
            "Д" => "d",
            "Е" => "e",
            "Ё" => "yo",
            "Ж" => "zh",
            "З" => "z",
            "И" => "i",
            "Й" => "y",
            "К" => "k",
            "Л" => "l",
            "М" => "m",
            "Н" => "n",
            "О" => "o",
            "П" => "p",
            "Р" => "r",
            "С" => "s",
            "Т" => "t",
            "У" => "u",
            "Ф" => "f",
            "Х" => "kh",
            "Ц" => "c",
            "Ч" => "ch",
            "Ш" => "sh",
            "Щ" => "sch",
            "Ъ" => "",
            "Ы" => "y",
            "Ь" => "",
            "Э" => "e",
            "Ю" => "yu",
            "Я" => "ya",
            // over
            "-" => "-",
            " " => "-",
            "_" => "_",
            "\\" => "-diff-",
            "/" => "-slash-",
            "+" => "-plus-",
            "=" => "-equal-",
            ":" => "-colon-",
            "." => "-dot-",
            // number
            "0" => "0",
            "1" => "1",
            "2" => "2",
            "3" => "3",
            "4" => "4",
            "5" => "5",
            "6" => "6",
            "7" => "7",
            "8" => "8",
            "9" => "9",
        ];

        if ($lower) {
            $str = static::getLower($str);
        }

        $result = '';
        $words = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);

        if (is_array($words) && count($words)) {
            foreach ($words as $value) {
                if (in_array($value, $ignoreChars)) {
                    $result .= $value;
                    continue;
                }

                // преобразовывает символы в их аналоги
                $result .= isset($char[$value]) && array_key_exists($value, $char) ? $char[$value] : $value;
            }
        }

        $ignore = count($ignoreChars) > 0 ? preg_quote(implode('', $ignoreChars), '/') : "";

        // Заменяем все не простые символы
        $result = preg_replace("/[^a-z0-9\-_{$ignore}]/ius", "-", $result);

        return trim(preg_replace("/[-]+/", '-', $result), '-'); // убираем дубли
    }
}
