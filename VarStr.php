<?php

namespace Warkhosh\Variable;

use DateTime;
use Exception;

/**
 * Class VarStr
 *
 * Класс по работе с строками
 *
 * @package Warkhosh\Variable
 */
class VarStr
{
    const ENCODING = 'UTF-8';

    /**
     * Пробельные символы
     */
    const SPACE_CHAR = [
        " ",        // пробел
        " ",     // No-break space = 0xC2,0xA0
        " ",     // En space       = 0xE2,0x80,0x82
        " ",     // Em space       = 0xE2,0x80,0x83
        " ",     // Thin space     = 0xE2,0x80,0x89
    ];

    /**
     * Пробельные коды
     */
    const SPACE_CODE = [
        "&nbsp;",   // Мнемоник пробела
        "&emsp;",   // Мнемоник очень длинного пробел, примерно с длинное тире
        "&ensp;",   // Мнемоник длинного пробела, примерно с короткое тире
        "&thinsp;", // Мнемоник узкого пробела
    ];

    /**
     * Управляющие символы
     */
    const CONTROL_CHAR = [
        "\n",       // Перевод каретки на следующую строку (0x0A)
        "\r",       // Перевод каретки на в начало текущей строки (0x0D)
        "\t",       // Табуляция (tab)
        "\v",       // Вертикальная табуляция (0x0B)
    ];

    /**
     * Управляющие символы
     */
    const SPECIAL_CHAR = [
        "\0",       // NUL-байт (0x00)
    ];

    /**
     * Преобразование переданного значения в текст
     *
     * @param mixed $str
     * @return string
     */
    static public function getMakeString($str = ''): string
    {
        if (gettype($str) === 'string') {
            return $str;
        }

        if (is_null($str) || is_array($str) || is_object($str)) {
            return '';
        }

        if (is_bool($str)) {
            return ($str === true ? 'true' : 'false');
        }

        return strval($str);
    }

    /**
     * Преобразование переданного значения в текст
     *
     * @param mixed $str
     * @return void
     */
    static public function makeString(&$str = '')
    {
        $str = static::getMakeString($str);
    }

    /**
     * Определить, является ли данная строка начинается с определенной подстроки
     *
     * @param string|null  $str
     * @param string|array $needles
     * @return bool
     */
    public static function startsWith(?string $str, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if ($needle != '' && mb_strpos((string)$str, $needle) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Определить, заканчивается ли строка с заданной подстроки
     *
     * @param string|null  $str
     * @param string|array $needles
     * @return bool
     */
    public static function endsWith(?string $str, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if ((string)$needle === mb_substr((string)$str, -mb_strlen($needle, self::ENCODING))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Регистрозависимый поиск первого вхождения символа в строке с возвратом номера позиции символа или false
     *
     * @note Первый символ стоит на позиции 0, позиция второго 1 и так далее.
     * @param string|array $needles - строка, поиск которой производится в строке $str
     * @param string|null  $str     - строка в которой ищем $needles
     * @param integer      $offset
     * @return integer|bool
     */
    public static function findPos($needles = null, ?string $str = '', int $offset = 0)
    {
        foreach ((array)$needles as $needle) {
            if (($pos = mb_strpos((string)$str, $needle, $offset, 'UTF-8')) !== false) {
                return $pos;
            }
        }

        return false;
    }

    /**
     * Регистрозависимый поиск первого вхождения символа в строке с возвратом результата
     *
     * @note Первый символ стоит на позиции 0, позиция второго 1 и так далее.
     * @param string|array $needles - строка, поиск которой производится в строке $str
     * @param string|null  $str     - строка в которой ищем $needles
     * @param int          $offset
     * @return bool
     */
    public static function find($needles = null, ?string $str = '', int $offset = 0): bool
    {
        if (empty($needles)) {
            return false;
        }

        if (static::findPos($needles, $str, $offset) !== false) {
            return true;
        }

        return false;
    }

    /**
     * Замена первого вхождения
     *
     * @param string      $search
     * @param string      $replace
     * @param string|null $str
     * @return string
     */
    static public function replaceOnce(string $search, string $replace, ?string $str): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        if ($search !== "") {
            $position = mb_strpos((string)$str, (string)$search, 0, self::ENCODING);

            if ($position !== false) {
                $start = mb_substr((string)$str, 0, $position, self::ENCODING);
                $endPosition = ($position + mb_strlen((string)$search, self::ENCODING));
                $end = mb_substr((string)$str, $endPosition, null, self::ENCODING);

                return $start . (string)$replace . $end;
            }
        }

        return $str;
    }

    /**
     * Замена первой найденной строки в строке
     *
     * @param string      $search
     * @param string      $replace
     * @param string|null $str
     * @return string
     * @deprecated метод будет переименован в replaceOnce()
     */
    static public function str_replace_once(string $search, string $replace, ?string $str): string
    {
        return static::replaceOnce($search, $replace, $str);
    }

    /**
     * Возвращает длину указанной строки
     *
     * @param string|null $value
     * @return int
     */
    public static function length(?string $value): int
    {
        return mb_strlen((string)$value, self::ENCODING);
    }

    /**
     * Устанавливает начало строки на указанное с проверкой на наличие такого значения
     *
     * @param string      $prefix
     * @param string|null $str
     * @return string
     */
    public static function start(string $prefix, ?string $str): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        if ($prefix !== "") {
            $quoted = preg_quote((string)$prefix, '/');

            return (string)$prefix . preg_replace('/^(?:' . $quoted . ')+/u', '', (string)$str);
        }

        return (string)$str;
    }

    /**
     * Закрывает строку заданным значением с проверкой на наличие такого значения
     *
     * @param string      $prefix
     * @param string|null $str
     * @return string
     */
    public static function ending(string $prefix, ?string $str): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        if ($prefix !== "") {
            $quoted = preg_quote((string)$prefix, '/');

            return preg_replace('/(?:' . $quoted . ')+$/u', '', (string)$str) . (string)$prefix;
        }

        return (string)$str;
    }

    /**
     * Убирает указное значения из начала строки
     *
     * @param string      $prefix
     * @param string|null $str
     * @return string
     */
    static public function getRemoveStart(string $prefix, ?string $str): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        if ($prefix !== "") {
            $quoted = preg_quote((string)$prefix, '/');

            return preg_replace('/^(?:' . $quoted . ')+/u', '', (string)$str);
        }

        return (string)$str;
    }

    /**
     * Убирает указное значения из конца строки
     *
     * @param string      $prefix
     * @param string|null $str
     * @return string
     */
    static public function getRemoveEnding(string $prefix, ?string $str): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        if ($prefix !== "") {
            $quoted = preg_quote((string)$prefix, '/');

            return preg_replace('/(?:' . $quoted . ')+$/u', '', (string)$str);
        }

        return (string)$str;
    }

    /**
     * Убирает указное значения из начала строки
     *
     * @param string      $search
     * @param string      $replace
     * @param string|null $str
     * @return string
     */
    static public function getReplaceStart(string $search, string $replace, ?string $str): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        if ($search !== "") {
            $quoted = preg_quote((string)$search, '/');

            return preg_replace('/^(?:' . $quoted . ')+/u', (string)$replace, (string)$str);
        }

        return (string)$str;
    }

    /**
     * Убирает указное значения из конца строки
     *
     * @param string      $search
     * @param string      $replace
     * @param string|null $str
     * @return string
     */
    static public function getReplaceEnding(string $search, string $replace, ?string $str): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        if ($search !== "") {
            $quoted = preg_quote((string)$search, '/');

            return preg_replace('/(?:' . $quoted . ')+$/u', (string)$replace, (string)$str);
        }

        return (string)$str;
    }

    /**
     * Кодирует данные в формат MIME base64
     *
     * @param string|null $str
     * @return string
     */
    static function getBase64UrlEncode(?string $str): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        return rtrim(strtr(base64_encode((string)$str), '+/', '-_'), '=');
    }

    /**
     * Декодирует данные, закодированные MIME base64
     *
     * @param string|null $str
     * @return string
     */
    static function getBase64UrlDecode(?string $str): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        $string = strtr((string)$str, '-_', '+/');
        $length = mb_strlen((string)$str, self::ENCODING) % 4;

        return base64_decode(str_pad($string, $length, '=', STR_PAD_RIGHT));
    }

    /**
     * Преобразовать данную строку в нижний регистр (lower-case).
     *
     * @note нужно учитывать что чем больше символов строке тем больше потребуется память при preg_split() !
     * @param string|null $words
     * @return string
     * @throws Exception
     */
    public static function getLower(?string $words): string
    {
        if (is_null($words) || $words === "") {
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
        $words = str_replace('+', '[=FIX_CHAR_PLUS_REPLACE=]', (string)$words);
        $words = urldecode($words);
        $words = str_replace('[=FIX_CHAR_PLUS_REPLACE=]', '+', $words);

        $words = VarStr::toUTF8($words);
        $words = mb_strtolower($words); // другие символы в нижний регистр
        $words = preg_split('//u', $words, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($words as $key => $row) {
            $str .= isset($replaceChar[$row]) ? $replaceChar[$row] : $row;
        }

        return $str;
    }

    /**
     * Преобразовать данную строку в верхний регистр (upper-case).
     *
     * @note нужно учитывать что чем больше символов строке тем больше потребуется память при preg_split() !
     * @param string|null $words
     * @return string
     * @throws Exception
     */
    static public function getUpper(?string $words): string
    {
        if (is_null($words) || $words === "") {
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
        $words = str_replace('+', '[=FIX_CHAR_PLUS_REPLACE=]', (string)$words);
        $words = urldecode($words);
        $words = str_replace('[=FIX_CHAR_PLUS_REPLACE=]', '+', $words);

        $words = VarStr::toUTF8($words);
        $words = mb_strtoupper($words, 'UTF-8'); // другие символы в верхний регистр
        $words = preg_split('//u', $words, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($words as $key => $row) {
            $str .= isset($replaceChar[$row]) ? $replaceChar[$row] : $row;
        }

        return $str;
    }

    /**
     * Ограничить количество слов в строке
     *
     * @note предназначен для работы с простыми кодировками
     * @param string|null $str
     * @param int         $words
     * @param string      $end
     * @return string
     */
    static public function words(?string $str, int $words = 100, string $end = '...')
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        if ($words >= 0) {
            preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $str, $matches);

            if (! isset($matches[0]) || mb_strlen($str, self::ENCODING) === mb_strlen($matches[0], self::ENCODING)) {
                return $str;
            }

            return rtrim($matches[0]) . $end;
        }

        return (string)$str;
    }

    /**
     * Обрезает строку до указанных символов
     *
     * @note метод использует trim() перед обрезанием
     *
     * @param string|null $str
     * @param null|int    $length
     * @return string
     */
    static public function crop(?string $str, int $length = 250): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        if ($length !== 0) {
            $str = VarStr::trim((string)$str);

            return mb_substr($str, 0, (int)$length, self::ENCODING);
        }

        return "";
    }

    /**
     * Сокращает текст по параметрам
     *
     * @note метод использует trim() перед сокращением
     *
     * @param string|null $str
     * @param int         $length
     * @param string      $ending
     * @param bool        $transform - преобразование кодов в символы и обратно ( дял подсчета длинны по символам )
     * @param bool        $smart     - флаг включающий умную систему усечения строки с учётом целостности слов
     * @return string
     */
    static public function reduce(
        ?string $str,
        int $length = 250,
        string $ending = '',
        bool $transform = true,
        bool $smart = true
    ): string {
        if ($length <= 0 || is_null($str) || $str === "") {
            return (string)$str;
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
     * Convert a value to studly caps case.
     *
     * @param string|null $str
     * @return string
     */
    public static function getStudly(?string $str): string
    {
        static $studlyCache;

        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        $str = ucwords(str_replace(['-', '_'], ' ', (string)$str));

        return str_replace(' ', '', $str);
    }

    /**
     * Преобразование Camel case в Snake case!
     *
     * @param string|null $str
     * @return string
     */
    static public function getSnakeCase(?string $str): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', (string)$str, $matches);
        $ret = (array)$matches[0];

        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }

    /**
     * Преобразование Snake case в Camel case!
     *
     * @param string|null $str
     * @return string
     */
    static public function getCamelCase(?string $str): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        return join("", VarArray::ucfirst(explode("_", (string)$str)));
    }

    /**
     * Преобразует HTML-сущности ( Entity ) в специальные символы
     *
     * @example: &amp;copy; > &copy; | &amp; > & | &quot; > " | &bull; > •
     *
     * @param string|null $str
     * @param int         $flags    - битовая маска из флагов определяющая режим обработки
     * @param string      $encoding - кодировка
     * @return string
     */
    static public function getHtmlEntityDecode(
        ?string $str,
        int $flags = ENT_COMPAT | ENT_HTML5,
        string $encoding = 'UTF-8'
    ): string {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        return html_entity_decode((string)$str, $flags, $encoding);
    }

    /**
     * Кодирует только специальные символы в их HTML-сущности
     *
     * @note    Кодирует только символы &, ", ', <, >, для кодирования всех символов используйте self::htmlEntityEncode()
     * @example & > &amp; | " > &quot; | ' > &apos; | > в &lt; | < в &gt;
     *
     * @param string|null $str
     * @param int         $flags        - битовая маска из флагов определяющая режим обработки
     * @param string      $encoding     - кодировка
     * @param bool        $doubleEncode - при выключении не будет преобразовывать существующие HTML-сущности. При включении приведет к преобразованию &apos; > &amp;&apos;
     * @return string
     */
    static public function getHtmlSpecialCharsEncode(
        ?string $str,
        int $flags = ENT_COMPAT | ENT_HTML5,
        string $encoding = 'UTF-8',
        bool $doubleEncode = false
    ): string {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        return htmlspecialchars((string)$str, $flags, $encoding, $doubleEncode);
    }

    /**
     * Кодирует ( все допустимые! ) символы в соответствующие HTML-сущности ( Entity )
     * Если надо преобразовать &copy; > &amp;copy; следует четвертый параметр $htmlEncode установить в TRUE
     *
     * @note    для преобразования только символов &, ", ', <, > используйте self::getHtmlSpecialCharsEncode() !
     * @example & > &amp; | " > &quot;
     *
     * @param string|null $str          -
     * @param int         $flags        - битовая маска из флагов определяющая режим обработки
     * @param string      $encoding     - кодировка
     * @param bool        $doubleEncode - при выключении не будет преобразовывать существующие HTML-сущности. При включении приведет к преобразованию &copy; > &amp;copy;
     * @return string
     */
    static public function getHtmlEntityEncode(
        ?string $str,
        int $flags = ENT_COMPAT | ENT_HTML5,
        string $encoding = 'UTF-8',
        bool $doubleEncode = false
    ): string {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        return $return = htmlentities((string)$str, $flags, $encoding, $doubleEncode);
    }

    /**
     * Декодирование закодированной URL строки
     *
     * @param string|null $str      - строка, которая должна быть декодирована.
     * @param bool        $raw      - флаг для переключения метода декодирования на rawurldecode() без преобразования символа +
     * @param string      $encoding - кодировка
     * @return string
     * @throws Exception
     */
    static public function getUrlDecode(?string $str = '', bool $raw = false, string $encoding = 'UTF-8'): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        $str = static::getTransformToEncoding((string)$str, $encoding);

        if ($raw) {
            return rawurldecode($str); // раскодирует контент по RFC 3986
        }

        // стараемся сохранить символ плюса
        $str = str_replace('+', '[=FIX_CHAR_PLUS_REPLACE=]', $str);
        $str = urldecode($str); // раскодирует контент по типу application/x-www-form-urlencoded где пробел это +
        $str = str_replace('[=FIX_CHAR_PLUS_REPLACE=]', '+', $str);

        return $str;
    }

    /**
     * Кодирование строки для URL
     *
     * @note RFC 3986: строка, в которой все не цифро-буквенные символы, кроме -_.~,
     *       должны быть заменены знаком процента (%) за которым следует два шестнадцатеричных числа
     *
     * @param string|null $str      - строка, которая должна быть декодирована.
     * @param bool        $raw      - флаг для переключения метода кодирования на rawurlencode() согласно RFC 3986 без преобразования символа +
     * @param string      $encoding - кодировка
     * @return string
     * @throws Exception
     */
    static public function getUrlEncode(?string $str = '', bool $raw = false, string $encoding = 'UTF-8'): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        $str = static::getTransformToEncoding((string)$str, $encoding);

        if ($raw) {
            return rawurlencode($str); // кодирует строку по RFC 3986
        }

        // Возвращает строку, в которой все не цифро-буквенные символы, кроме -_. должны быть заменены знаком процента (%),
        // за которым следует два шестнадцатеричных числа, а пробелы закодированы как знак сложения (+).
        // Строка кодируется тем же способом, что и POST-данные веб-формы, то есть по типу контента application/x-www-form-urlencoded
        return urlencode($str);
    }

    /**
     * Форматирует переданное значение с разделением групп
     *
     * @param mixed  $str
     * @param int    $decimals      - точность (цело число больше нуля)
     * @param string $separator     - разделитель точности
     * @param string $thousands_sep - разделитель тысяч
     * @param int    $default
     * @return string
     * @throws Exception
     */
    static public function getNumberFormat(
        $str,
        int $decimals = 2,
        string $separator = '.',
        string $thousands_sep = '',
        $default = 0
    ): string {
        $decimals = $decimals > 0 ? $decimals : 2;

        if (is_null($str) || $str === "") {
            return number_format($default, $decimals, $separator, $thousands_sep);
        }

        if (is_string($str)) {
            $price = preg_replace('/[^0-9\,\.\s]/', '', (string)$str);

            if ($str !== $price) {
                throw new \Exception("Incorrect number");
            }

            $str = static::trim($str);
            $dotParts = explode(".", $str);
            $commaParts = explode(",", $str);
            $spaceParts = explode(" ", $str);

            $fidDot = count($dotParts) > 1;
            $fidComma = count($commaParts) > 1;
            $fidSpace = count($spaceParts) > 1;

            // Проверка наличия только одного разделителя в тексте
            if (((int)$fidDot + (int)$fidComma + (int)$fidSpace) > 1) {
                throw new \Exception("There are several separators in value of number");
            }

            // Проверка повторяющегося разделителя
            if (($fidDot + $fidComma + $fidSpace) > 1) {
                throw new \Exception("Invalid number format");
            }
        }

        $str = VarFloat::getMake($str, $decimals, $default);

        return number_format($str, $decimals, $separator, $thousands_sep);
    }

    /**
     * Создает токен по двум алгоритмам
     *
     * @note
     * @param integer $length   - длинна возвращаемых символов (без учёта $split)
     * @param integer $split    - количество символов для разделения строки на части через символ тире
     * @param boolean $readable - флаг для генерации более простых кодов
     * @return string
     * @throws Exception
     */
    static public function getRandomToken(int $length = 128, int $split = 0, $readable = false): string
    {
        if (! ($length > 0)) {
            throw new Exception("Specify the length to generate the string");
        }

        $unreadablePool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $readablePool = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $times = (int)VarFloat::round($length / 10, 0, 'upward');
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
     * @param integer $length   - длинна возвращаемых символов (без учёта $split)
     * @param integer $split    - количество символов для разделения строки на части через символ тире
     * @param boolean $readable - флаг для генерации более простых кодов
     * @return string
     * @throws Exception
     * @deprecated изменилось название метода getRandomToken()
     */
    static public function randomToken(int $length = 128, int $split = 0, $readable = false): string
    {
        return static::getRandomToken($length, $split, $readable);
    }

    /**
     * Создает цифирный код
     *
     * @param int $length - длинна возвращаемых символов (без учёта $split)
     * @param int $split  - количество символов для разделения строки на части через символ тире
     * @return string
     * @throws Exception
     */
    static public function getRandomNumberCode(int $length = 6, int $split = 0): string
    {
        if (! ($length > 0)) {
            throw new Exception("Specify the length to generate the string");
        }

        $pool = '0123456789';
        $times = (int)VarFloat::round($length / 10, 0, 'upward');
        $str = substr(str_shuffle(str_repeat($pool, $times)), 0, $length);

        if ($split > 0) {
            $str = join('-', str_split($str, $split));
        }

        return (string)$str;
    }

    /**
     * Создает цифирный код
     *
     * @param int $length - длинна возвращаемых символов (без учёта $split)
     * @param int $split  - количество символов для разделения строки на части через символ тире
     * @return string
     * @throws Exception
     * @deprecated изменилось название метода getRandomToken()
     */
    static public function randomNumberCode(int $length = 6, int $split = 0): string
    {
        return static::getRandomNumberCode($length, $split);
    }

    /**
     * Возвращает часть строки
     *
     * @param string|null $str
     * @param int         $start
     * @param int|null    $length
     * @return string
     */
    static public function substr(?string $str, int $start, ?int $length = null): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        return mb_substr((string)$str, $start, $length, 'UTF-8');
    }

    /**
     * Преобразует первый символ строки в верхний регистр
     *
     * @param string|null $str
     * @return string
     * @throws Exception
     */
    static public function ucfirst(?string $str): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        return static::getUpper(static::substr((string)$str, 0, 1)) . static::substr((string)$str, 1);
    }

    /**
     * Убирает из строки разные пробелы и технические символы
     *
     * @note промежуточный метод для последующих проверок на пустоту в другим местах
     *
     * @param string|null $str
     * @return string
     */
    static public function getClean(?string $str): string
    {
        if (is_null($str) || $str === "") {
            //$search = ["\n", "\t", "\r", '&nbsp;', '&emsp;', '&ensp;', '&thinsp;'];
            $search = array_merge(static::SPACE_CHAR, static::SPACE_CODE, static::CONTROL_CHAR);
            $str = str_replace($search, ' ', (string)$str);
            $str = static::trim(preg_replace('/\s{2,}/', ' ', $str), "\x00..\x1F"); // убрать лишние пробелы

            return $str;
        }

        return (string)$str;
    }

    /**
     * Проверка строки на на пустое значение.
     *
     * @param string|null $str
     * @return bool
     */
    static public function isEmpty(?string $str)
    {
        if (is_null($str) || is_bool($str) || is_array($str)) {
            return true;
        }

        if ($str !== "") {
            $str = str_replace(static::CONTROL_CHAR, '', (string)$str);
            $str = str_replace(static::SPECIAL_CHAR, '', $str);
            $str = str_replace(static::SPACE_CODE, ' ', $str);
            $str = str_replace(static::SPACE_CHAR, ' ', $str);
            $str = static::trim($str);

            return preg_match("/(\S+)/i", $str) == 0;
        }

        return true;
    }

    /**
     * Удаляет пробелы из начала и конца строки (или другие символы при передачи их вторым параметром )
     *
     * @note \x0B вертикальная табуляция,
     *
     * @param string|null $str
     * @param string      $remove             - список символов для удаления
     * @param bool        $addSingleSpaceChar - флаг добавляющий к списку символов односимвольные пробелы
     * @return string
     */
    static public function trim(?string $str, $remove = " \t\n\r\v\0\x0B", bool $addSingleSpaceChar = true): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        // Дописываем к переданному списку символов односимвольные пробелы !
        if ($addSingleSpaceChar === true) {
            // Следить и не допускать сюда обычные символы!
            $remove .=
                // No-break space
                chr(0xC2) . chr(0xA0) .
                // 'En space
                chr(0xE2) .
                chr(0x80) .
                chr(0x82) .
                // Em space
                chr(0xE2) .
                chr(0x80) .
                chr(0x83) .
                // Thin space
                chr(0xE2) .
                chr(0x80) .
                chr(0x89) .
                // удаляем управляющие ASCII-символы с начала и конца $binary (от 0 до 31 включительно)
                "\x00..\x1F";
        }

        return trim((string)$str, (string)$remove);
    }

    /**
     * Замена повторяющихся символов из списка $char в значения из списка $replace
     *
     * @note нужно учитывать что список $replace должен совпадать по длине с $char
     *
     * @param string|null  $str
     * @param string|array $char
     * @param string|array $replace
     * @return string
     */
    static public function getRemovingDoubleChar(?string $str = '', array $char = [' '], array $replace = [' ']): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        $char = is_array($char) ? $char : [(string)$char];
        $replace = is_array($replace) ? $replace : [(string)$replace];

        foreach ($char as $key => $symbol) {
            if (isset($replace[$key])) {
                $quoted = preg_quote($symbol, '/');
                $str = static::trim(preg_replace("/" . $quoted . "{2,}/", $replace[$key], (string)$str));
            }
        }

        return (string)$str;
    }

    /**
     * Удаление указанных символов из строки
     *
     * @param string|null  $str
     * @param array|string $removeChar - список символов для удаления
     * @return string
     */
    static public function getRemoveSymbol(?string $str, $removeChar = ["\n", "\r", "\t", "\v"]): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        $removeChar = is_array($removeChar) ? $removeChar : [(string)$removeChar];

        if (count($removeChar) > 0) {
            $str = str_replace($removeChar, '', (string)$str);
        }

        return (string)$str;
    }

    /**
     * Проверка даты под указанный формат
     *
     * @param string|null $strDate
     * @param string      $format
     * @return bool
     */
    static public function validateDateTime(?string $strDate = null, $format = 'Y-m-d H:i:s')
    {
        if (is_null($strDate) || $strDate === "") {
            return false;
        }

        $dt = DateTime::createFromFormat($format, (string)$strDate);

        return $dt && $dt->format($format) === $strDate;
    }

    /**
     * Проверка строки на дату по указанному формату
     *
     * @param string|null $strDate
     * @param mixed       $default
     * @param string      $format
     * @return string|bool|null
     */
    static public function makeDate(?string $strDate, $default = null, $format = 'Y-m-d')
    {
        if (is_null($strDate) || $strDate === "") {
            return (string)$default;
        }

        $strDate = VarStr::trim((string)$strDate);
        $strDate = mb_substr($strDate, 0, 250, self::ENCODING);

        return VarStr::validateDateTime($strDate, $format) === true ? $strDate : (string)$default;
    }

    /**
     * Возвращает название кодировки у переданной строки
     *
     * @param string|null $str
     * @return string|null
     */
    static public function getEncoding(?string $str): ?string
    {
        //$encodingList = ['utf-8', 'windows-1251', /*'ascii'*/];
        $currentEncoding = mb_detect_encoding((string)$str, mb_detect_order(), false);
        //$cleanStr = (string)$str;

        switch ($currentEncoding) {
            case 'UTF-8':
                $text = mb_convert_encoding((string)$str, 'UTF-8');
                break;

            case 'Windows-1251':
                $text = mb_convert_encoding((string)$str, 'Windows-1251');
                break;

            //case 'ASCII':
            //    $text = mb_convert_encoding((string)$str, 'ASCII');
            //    break;

            default:
                $text = (string)$str;
        }

        if ($text !== false) {
            return $currentEncoding;
        }

        //foreach ($encodingList as $k => $codePage) {
        //    if (md5($str) === @md5(@iconv($codePage, $codePage . '//IGNORE', $text))) {
        //        return $codePage;
        //    }
        //}

        return null;
    }

    /**
     * Безопасное преобразование строки в utf-8
     *
     * @param string|null $str
     * @return string
     * @throws Exception
     */
    static public function toUTF8(?string $str = ''): string
    {
        // Если кодировки строки отличается от указанной
        if (! mb_check_encoding((string)$str, "UTF-8")) {
            $encoding = mb_detect_encoding((string)$str, mb_detect_order(), false);

            switch ($encoding) {
                case 'UTF-8':
                    $str = mb_convert_encoding((string)$str, 'UTF-8', 'UTF-8');
                    break;

                case 'ASCII':
                    $str = mb_convert_encoding((string)$str, 'UTF-8', 'ASCII');
                    break;

                case 'Windows-1251':
                    $str = mb_convert_encoding((string)$str, 'UTF-8', 'Windows-1251');
                    break;
            }

            // Проверка некорректной работы mb_convert_encoding()
            if ($str === false) {
                throw new Exception("Error when trying to convert a string via mb_convert_encoding()");
            }

            // Если было не удачное определение кодировки
            if (is_null($encoding)) {
                return @iconv("UTF-8", "UTF-8//IGNORE", (string)$str);
            }

            return @iconv($encoding, "UTF-8//IGNORE", $str);
        }

        return (string)$str;
    }

    /**
     * Безопасное преобразование строки в указанную кодировку если она таковой не является
     *
     * @param string|null $str      - строка, для которой требуется определить кодировку
     * @param string      $encoding - ожидаемая кодировка
     * @return string
     * @throws Exception
     */
    static public function getTransformToEncoding(?string $str = '', string $encoding = 'UTF-8'): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        // Если кодировки строки отличается от указанной
        if (! mb_check_encoding((string)$str, $encoding)) {
            // Определение кодировки у указанной строки
            $currentEncoding = mb_detect_encoding((string)$str, mb_detect_order(), false);

            // Преобразование строки из одной кодировки символов в другую
            $text = mb_convert_encoding((string)$str, $encoding);

            // Проверка некорректной работы mb_convert_encoding()
            if ($text === false) {
                throw new Exception("Error when trying to convert a string via mb_convert_encoding()");
            }

            // Если было не удачное определение кодировки у указанной строки
            if (is_null($currentEncoding)) {
                return @iconv("UTF-8", "UTF-8//IGNORE", $text);
            }

            return @iconv($currentEncoding, "{$encoding}//IGNORE", $text);
        }

        return (string)$str;
    }

    /**
     * Разбивает строку по разделителю и дополнительно производит удаление пустых значений;
     *
     * @param string      $delimiter - разделитель
     * @param string|null $str       - строка
     * @param array       $deleted   - массив значений которые надо удалить
     * @return array
     */
    static public function explode(string $delimiter, ?string $str, array $deleted = ['', 0, null]): array
    {
        if (is_null($str) || $str === "") {
            return [];
        }

        if (is_array($deleted) && count($deleted) > 0) {
            $parts = explode($delimiter, static::trim((string)$str));

            return VarArray::getRemove($parts, $deleted);

        }

        return explode($delimiter, static::trim((string)$str));
    }

    /**
     * Разбивает строку по разделителю и дополнительно производит удаление пустых значений;
     *
     * @param string      $delimiter - разделитель
     * @param string|null $str       - строка
     * @param string      $action    - id|ids|number|integer
     * @return array
     */
    static public function explodeToNumber(string $delimiter, ?string $str, $action = "ids"): array
    {
        if (is_null($str) || $str === "") {
            return [];
        }

        $ids = explode($delimiter, static::trim((string)$str));

        switch ($action) {
            case 'ids':
            case 'id':
                foreach ($ids as $key => $id) {
                    $int = intval($id);

                    if (! ($id > 0 && $int == $id)) {
                        unset($ids[$key]);
                        continue;
                    }

                    $ids[$key] = $int;
                }

                break;

            case 'integer':
                foreach ($ids as $key => $id) {
                    $int = intval($id);

                    if ($id != $int) {
                        unset($ids[$key]);
                        continue;
                    }

                    $ids[$key] = $int;
                    continue;
                }

                break;

            case 'number':
                foreach ($ids as $key => $id) {
                    if (! is_numeric($id)) {
                        unset($ids[$key]);
                        continue;
                    }

                    $int = intval($id);

                    if (is_integer($id)) {
                        $ids[$key] = $int;
                        continue;
                    }

                    if (is_string($id)) {
                        // int
                        if ($id == $int) {
                            $ids[$key] = $int;
                            continue;
                        }

                        // float
                        $float = VarFloat::makeString($id);

                        if ($id == $float) {
                            $ids[$key] = $float;
                            continue;
                        }
                    }

                    unset($ids[$key]);
                    continue;
                }

                break;
        }

        return $ids;
    }

    /**
     * Преобразует HTML-сущности в специальные символы через регулярное выражение и список сущностей.
     * Преобразует &amp; > & | &quot; > " | &bull; > •
     *
     * @param string|null $str
     * @param int         $flags
     * @param string      $charset = utf-8 (ISO-8859-1)
     * @return string
     * @todo это решение на php4, но пока решил оставить на всякий случай
     *
     */
    static function getDecodeEntities(?string $str, $flags = ENT_COMPAT, $charset = 'UTF-8')
    {
        $str = preg_replace_callback('/&([a-zA-Z][a-zA-Z0-9]+);/', 'convert_entity', (string)$str);

        return html_entity_decode($str, $flags, $charset);
    }

    /**
     * Транскрипция текста
     *
     * @param string $str
     * @param bool   $lower
     * @return string
     * @throws Exception
     */
    static public function getTranscription(string $str, bool $lower = true): string
    {
        if (is_null($str) || $str === "") {
            return (string)$str;
        }

        $str = static::getTransformToEncoding((string)$str);
        $str = strip_tags(static::trim($str));
        $str = urldecode($str); // Декодирование URL из кодированной строки

        $char = [
            // lower
            "а"  => "a",
            "б"  => "b",
            "в"  => "v",
            "г"  => "g",
            "д"  => "d",
            "е"  => "e",
            "ё"  => "yo",
            "ж"  => "zh",
            "з"  => "z",
            "и"  => "i",
            "й"  => "y",
            "к"  => "k",
            "л"  => "l",
            "м"  => "m",
            "н"  => "n",
            "о"  => "o",
            "п"  => "p",
            "р"  => "r",
            "с"  => "s",
            "т"  => "t",
            "у"  => "u",
            "ф"  => "f",
            "х"  => "kh",
            "ц"  => "c",
            "ч"  => "ch",
            "ш"  => "sh",
            "щ"  => "sch",
            "ъ"  => "",
            "ы"  => "y",
            "ь"  => "",
            "э"  => "e",
            "ю"  => "yu",
            "я"  => "ya",
            // upper
            "А"  => "a",
            "Б"  => "b",
            "В"  => "v",
            "Г"  => "g",
            "Д"  => "d",
            "Е"  => "e",
            "Ё"  => "yo",
            "Ж"  => "zh",
            "З"  => "z",
            "И"  => "i",
            "Й"  => "y",
            "К"  => "k",
            "Л"  => "l",
            "М"  => "m",
            "Н"  => "n",
            "О"  => "o",
            "П"  => "p",
            "Р"  => "r",
            "С"  => "s",
            "Т"  => "t",
            "У"  => "u",
            "Ф"  => "f",
            "Х"  => "kh",
            "Ц"  => "c",
            "Ч"  => "ch",
            "Ш"  => "sh",
            "Щ"  => "sch",
            "Ъ"  => "",
            "Ы"  => "y",
            "Ь"  => "",
            "Э"  => "e",
            "Ю"  => "yu",
            "Я"  => "ya",
            // over
            "-"  => "-",
            " "  => "-",
            "\\" => "-diff-",
            "/"  => "-slash-",
            "+"  => "-plus-",
            "="  => "-equal-",
            ":"  => "-colon-",
            "."  => "-dot-",
            // number
            "0"  => "0",
            "1"  => "1",
            "2"  => "2",
            "3"  => "3",
            "4"  => "4",
            "5"  => "5",
            "6"  => "6",
            "7"  => "7",
            "8"  => "8",
            "9"  => "9",
        ];

        if ($lower) {
            $str = static::getLower($str);
        }

        $result = '';
        $words = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);

        if (is_array($words) && count($words)) {
            foreach ($words as $key => $value) {
                // преобразовывает русские символы в их аналоги
                $result .= isset($char[$value]) && array_key_exists($value, $char) ? $char[$value] : $value;
            }
        }

        return preg_replace("/[-]+/", '-', $result); // убираем дубли
    }
}