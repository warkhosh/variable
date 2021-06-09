<?php

namespace Warkhosh\Variable;

use DateTime;
use Warkhosh\Variable\Helper\Helper;

class VarStr
{
    /**
     * Преобразование переданного значения в текст.
     *
     * @param mixed $str
     * @return string
     */
    static public function getMakeString($str = '')
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
     * Преобразование переданного значения в текст.
     *
     * @param mixed $str
     * @return void
     */
    static public function makeString(&$str = '')
    {
        $str = static::getMakeString($str);
    }


    /**
     * Определить, является ли данная строка начинается с определенной подстроки.
     *
     * @param string       $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }


    /**
     * Определить, заканчивается ли строка с заданной подстроки.
     *
     * @param string       $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function endsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ((string)$needle === mb_substr($haystack, -mb_strlen($needle))) {
                return true;
            }
        }

        return false;
    }


    /**
     * Регистрозависимый поиск первого вхождения символа в строке с возвратом номера позиции символа или false
     *
     * @note Первый символ стоит на позиции 0, позиция второго 1 и так далее.
     * @param string | array $needles - строка, поиск которой производится в строке $str
     * @param string         $str     - строка в которой ищем $needles
     * @param integer        $offset
     * @return integer|bool
     */
    public static function findPos($needles = null, $str = '', $offset = 0)
    {
        foreach ((array)$needles as $needle) {
            if (($pos = mb_strpos($str, $needle, $offset, 'UTF-8')) !== false) {
                return $pos;
            }
        }

        return false;
    }


    /**
     * Регистрозависимый поиск первого вхождения символа в строке с возвратом результата
     *
     * @note Первый символ стоит на позиции 0, позиция второго 1 и так далее.
     * @param string | array $needles - строка, поиск которой производится в строке $str
     * @param string         $str     - строка в которой ищем $needles
     * @param int            $offset
     * @return bool
     */
    public static function find($needles = null, $str = '', $offset = 0)
    {
        if (static::findPos($needles, $str, $offset) !== false) {
            return true;
        }

        return false;
    }


    /**
     * Замена первого вхождения
     *
     * @param string $search
     * @param string $replace
     * @param string $text
     * @return string
     */
    static public function replaceOnce($search = '', $replace = '', $text = '')
    {
        if (gettype($search) !== 'string') {
            $search = VarStr::getMakeString($search);
        }

        if (gettype($replace) !== 'string') {
            $replace = VarStr::getMakeString($replace);
        }

        if (gettype($text) !== 'string') {
            $text = VarStr::getMakeString($text);
        }

        if (mb_strlen($search) > 0) {
            $pos = mb_strpos($text, $search);

            if ($pos !== false) {
                return mb_substr($text, 0, $pos) . $replace . mb_substr($text, $pos + mb_strlen($search));
            }
        }

        return $text;
    }


    /**
     * Возвращает длину указанной строки.
     *
     * @param string $value
     * @return int
     */
    public static function length($value = '')
    {
        if (gettype($value) === 'string') {
            return mb_strlen($value);
        }

        return 0;
    }


    /**
     * Устанавливает начало строки на указанное с проверкой на наличие такого значения
     *
     * @param string $prefix
     * @param string $str
     * @return string
     */
    public static function start(string $prefix, ?string $str): string
    {
        $quoted = preg_quote($prefix, '/');
        $str = is_string($str) ? $str : "";

        return $prefix . preg_replace('/^(?:' . $quoted . ')+/u', '', $str);
    }


    /**
     * Закрывает строку заданным значением с проверкой на наличие такого значения
     *
     * @param string $prefix
     * @param string $str
     * @return string
     */
    public static function ending(string $prefix, ?string $str): string
    {
        $quoted = preg_quote($prefix, '/');
        $str = is_string($str) ? $str : "";

        return preg_replace('/(?:' . $quoted . ')+$/u', '', $str) . $prefix;
    }


    /**
     * Убирает указное значения из начала строки
     *
     * @param string|array $prefix
     * @param string       $str
     * @return string
     */
    static public function getRemoveStart($prefix = '', $str = '')
    {
        $str = static::getMakeString($str);

        if (gettype($prefix) === 'array') {
            foreach ($prefix as $text) {
                $str = preg_replace('/^(?:' . preg_quote($text, '/') . ')+/u', '', $str);
            }

            return $str;
        }

        if (gettype($prefix) === 'string') {
            return preg_replace('/^(?:' . preg_quote($prefix, '/') . ')+/u', '', $str);
        }

        return $str;
    }


    /**
     * Убирает указное значения из конца строки
     *
     * @param string|array $prefix
     * @param string       $str
     * @return string
     */
    static public function getRemoveEnding($prefix = '', $str = '')
    {
        $str = static::getMakeString($str);

        if (gettype($prefix) === 'array') {
            foreach ($prefix as $text) {
                $str = preg_replace('/(?:' . preg_quote($text, '/') . ')+$/u', '', $str);
            }

            return $str;
        }

        if (gettype($prefix) === 'string') {
            return preg_replace('/(?:' . preg_quote($prefix, '/') . ')+$/u', '', $str);
        }

        return $str;
    }


    /**
     * Убирает указное значения из начала строки
     *
     * @param string|array $search
     * @param string       $replace
     * @param string       $str
     * @return string
     */
    static public function getReplaceStart($search = '', $replace = '', $str = '')
    {
        $str = static::getMakeString($str);

        if (gettype($search) === 'array') {
            foreach ($search as $text) {
                $str = self::getReplaceStart($text, $replace, $str);
            }

            return $str;
        }

        if (gettype($search) === 'string') {
            return preg_replace('/^(?:' . preg_quote($search, '/') . ')+/u', '', $str);
        }

        return $str;
    }


    /**
     * Убирает указное значения из конца строки
     *
     * @param string|array $search
     * @param string       $replace
     * @param string       $str
     * @return string
     */
    static public function getReplaceEnding($search = '', $replace = '', $str = '')
    {
        $str = static::getMakeString($str);

        if (is_array($search)) {
            foreach ($search as $text) {
                $str = self::getReplaceEnding($text, $replace, $str);
            }

            return $str;
        }

        if (gettype($search) === 'string') {
            return preg_replace('/(?:' . preg_quote($search, '/') . ')+$/u', $replace, $str);
        }

        return $str;
    }


    /**
     * Кодирует данные в формат MIME base64
     *
     * @param string $str
     * @return string
     */
    static function getBase64UrlEncode($str = '')
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }


    /**
     * Декодирует данные, закодированные MIME base64
     *
     * @param string $str
     * @return string
     */
    static function getBase64UrlDecode($str = '')
    {
        return base64_decode(str_pad(strtr($str, '-_', '+/'), strlen($str) % 4, '=', STR_PAD_RIGHT));
    }


    /**
     * Преобразовать данную строку в нижний регистр (lower-case).
     *
     * @param string|array $words
     * @return string|array
     */
    public static function getLower($words)
    {
        if (is_array($words)) {
            foreach ($words as $key => $row) {
                $words[$key] = static::getLower($row);
            }

            return $words;
        }

        $words = static::getMakeString($words); // а что, а вдруг? :)

        $replaceChar = [
            "А" => "а",
            "Б" => "б",
            "В" => "в",
            "Г" => "г",
            "Д" => "д",
            "Е" => "е",
            "Ё" => "ё",
            "Ж" => "ж",
            "К" => "к",
            "Л" => "л",
            "М" => "м",
            "Н" => "н",
            "Ц" => "ц",
            "Ч" => "ч",
            "Ш" => "ш",
            "О" => "о",
            "Щ" => "щ",
            "П" => "п",
            "Ъ" => "ъ",
            "Р" => "р",
            "Ы" => "ы",
            "С" => "с",
            "Ь" => "ь",
            "З" => "з",
            "Т" => "т",
            "Э" => "э",
            "Х" => "х",
            "И" => "и",
            "У" => "у",
            "Ю" => "ю",
            "Й" => "й",
            "Ф" => "ф",
            "Я" => "я",
        ];

        $str = "";

        // специально сохраняем символ плюса
        $words = str_replace('+', '[=FIX_CHAR_PLUS_REPLACE=]', $words);
        $words = urldecode($words);
        $words = str_replace('[=FIX_CHAR_PLUS_REPLACE=]', '+', $words);

        if (Helper::getEncoding($words) == 'windows-1251') {
            $words = iconv('CP1251', 'UTF-8', $words);
        }

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
     * @param string|array $words
     * @return string|array
     */
    static public function getUpper($words)
    {
        if (is_array($words)) {
            foreach ($words as $key => $row) {
                $words[$key] = static::getUpper($row);
            }

            return $words;
        }

        $words = static::getMakeString($words); // а что, а вдруг? :)

        $replaceChar = [
            "а" => "А",
            "к" => "К",
            "х" => "Х",
            "б" => "Б",
            "л" => "Л",
            "ц" => "Ц",
            "в" => "В",
            "м" => "М",
            "ч" => "Ч",
            "г" => "Г",
            "н" => "Н",
            "ш" => "Ш",
            "д" => "Д",
            "о" => "О",
            "щ" => "Щ",
            "е" => "Е",
            "п" => "П",
            "ъ" => "Ъ",
            "ё" => "Ё",
            "р" => "Р",
            "ы" => "Ы",
            "ж" => "Ж",
            "с" => "С",
            "ь" => "Ь",
            "з" => "З",
            "т" => "Т",
            "э" => "Э",
            "и" => "И",
            "у" => "У",
            "ю" => "Ю",
            "й" => "Й",
            "ф" => "Ф",
            "я" => "Я",
        ];

        $str = "";

        // специально сохраняем символ плюса
        $words = str_replace('+', '[=FIX_CHAR_PLUS_REPLACE=]', $words);
        $words = urldecode($words);
        $words = str_replace('[=FIX_CHAR_PLUS_REPLACE=]', '+', $words);

        if (Helper::getEncoding($words) == 'windows-1251') {
            $words = iconv('CP1251', 'UTF-8', $words);
        }

        $words = mb_strtoupper($words, 'UTF-8'); // другие символы в верхний регистр
        $words = preg_split('//u', $words, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($words as $key => $row) {
            $str .= isset($replaceChar[$row]) ? $replaceChar[$row] : $row;
        }

        return $str;
    }


    /**
     * Ограничить количество слов в строке.
     *
     * @param string $value
     * @param int    $words
     * @param string $end
     * @return string
     */
    static public function words($value, $words = 100, $end = '...')
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $value, $matches);

        if (! isset($matches[0]) || strlen($value) === strlen($matches[0])) {
            return $value;
        }

        return rtrim($matches[0]) . $end;
    }


    /**
     * Обрезает строку до указаных символов;
     *
     * @param string   $str
     * @param null|int $length
     *
     * @return string
     */
    static public function crop($str = '', $length = 250)
    {
        if (is_null($length)) {
            return VarStr::getMakeString($str);
        }

        $length = VarInt::getMakePositiveInteger($length) > 0 ? intval($length) : 250;
        $default = VarStr::trim(VarStr::getMakeString($str));

        if (is_string($str) && mb_strlen($str) > 1) {
            $default = mb_substr($default, 0, $length);
            $default = is_bool($default) ? '' : $default;
        }

        return $default;
    }


    /**
     * Сокращает текст по параметрам;
     *
     * @param string    $str
     * @param null|int  $length
     * @param string    $end
     * @param bool|true $transform - преобразование кодов в символы и обратно ( дял подсчета длинны по символам )
     * @param bool|true $smart     - флаг включающий умную систему усечения строки с учётом целостности слов
     * @return string
     */
    static public function reduce($str = '', $length = 250, $end = '', $transform = true, $smart = true)
    {
        if (is_null($length)) {
            return $str;
        }

        $str = VarStr::trim(VarStr::getMakeString($str));

        // Допустимая длинна текста
        $length = VarInt::getMakePositiveInteger($length) > 0 ? intval($length) : 250;

        // вычисляем длинну текста с учетом количества символов от переменной обрезания
        $maxLength = VarInt::getMakePositiveInteger($length - mb_strlen($end));
        $end = mb_strlen($str) > $maxLength ? $end : '';

        // Защита от человеческого фактора когда длинна строки меньше чем окончание
        if ($maxLength < 1) {
            return '';
        }

        $smart = Helper::isTrue($smart);
        $transform = Helper::isTrue($transform);

        // Кодирует коды HTML-сущностей в символы для более точного усечения
        if ($transform) {
            $str = static::getHtmlEntityDecode($str);
        }

        // Жёсткое обрезание текста, строго по лимиту
        if (! $smart) {
            $str = VarStr::crop($str, $length) . $end;

            // Кодирует символы в HTML-сущности если указали флаг преобразования
            return $transform ? static::getHtmlSpecialCharsEncode($str) : $str;
        }

        // Длинна строки больше чем требуется
        if (mb_strlen($str) > $length) {
            // Укорачиваем единственное слово по точному количеству символов раз в строке нет пробелов
            if (mb_strstr($str, ' ') === false) {
                $str = mb_substr($str, 0, $maxLength);

            } else {
                $words = [];

                foreach (explode(" ", $str) as $string) {
                    if (mb_strlen(join(' ', $words)) < $maxLength) {
                        $words[] = $string;
                    }
                }

                // страховка на случай если первое и единственное выбраное слово превышает указанную длину
                if (count($words) === 1) {
                    $str = mb_substr($words[0], 0, $maxLength);

                } else {
                    array_pop($words); // убираем последнее слово делающее превышение ограничения по длинне
                    $str = static::trim(join(' ', $words));
                }
            }

            $str .= $end;
        }

        if ($transform) {
            $str = static::getHtmlSpecialCharsEncode($str); // Кодирует символы в HTML-сущности
        }

        return $str;
    }


    /**
     * Convert a value to studly caps case.
     *
     * @param string $value
     * @return string
     */
    public static function getStudly($value)
    {
        static $studlyCache;

        $key = $value;

        if (isset($studlyCache[$key])) {
            return $studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return $studlyCache[$key] = str_replace(' ', '', $value);
    }


    /**
     * Преобразование Camel case в Snake case!
     *
     * @param string $input
     * @return string
     */
    static public function getSnakeCase($input = '')
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }


    /**
     * Преобразование Snake case в Camel case!
     *
     * @param string $input
     * @return string
     */
    static public function getCamelCase($input = '')
    {
        return join("", VarArray::ucfirst(explode("_", $input)));
    }


    /**
     * Преобразует HTML-сущности ( Entity ) в специальные символы
     *
     * @example: &amp;copy; > &copy; | &amp; > & | &quot; > " | &bull; > •
     *
     * @param string $str
     * @param int    $flags    - битовая маска из флагов определяющая режим обработки
     * @param string $encoding - кодировка
     * @return array|string
     */
    static public function getHtmlEntityDecode($str, $flags = ENT_COMPAT | ENT_HTML5, $encoding = 'UTF-8')
    {
        return html_entity_decode((string)$str, $flags, $encoding);
    }


    /**
     * Кодирует только специальные символы в их HTML-сущности
     *
     * @note    Кодирует только символы &, ", ', <, >, для кодирования всех символов используйте self::htmlEntityEncode()
     * @example & > &amp; | " > &quot; | ' > &apos; | > в &lt; | < в &gt;
     *
     * @param string $str
     * @param int    $flags        - битовая маска из флагов определяющая режим обработки
     * @param string $encoding     - кодировка
     * @param bool   $doubleEncode - при выключении не будет преобразовывать существующие HTML-сущности. При включении приведет к преобразованию &apos; > &amp;&apos;
     * @return string
     */
    static public function getHtmlSpecialCharsEncode(
        $str,
        $flags = ENT_COMPAT | ENT_HTML5,
        $encoding = 'UTF-8',
        $doubleEncode = false
    ) {
        return htmlspecialchars((string)$str, $flags, $encoding, $doubleEncode);
    }


    /**
     * Кодирует ( все допустимые! ) символы в соответствующие HTML-сущности ( Entity )
     * Если надо преобразовать &copy; > &amp;copy; следует четвертый параметр $htmlEncode установить в TRUE
     *
     * @note    для преобразования только символов &, ", ', <, > используйте self::getHtmlSpecialCharsEncode() !
     * @example & > &amp; | " > &quot;
     *
     * @param string $str          -
     * @param int    $flags        - битовая маска из флагов определяющая режим обработки
     * @param string $encoding     - кодировка
     * @param bool   $doubleEncode - при выключении не будет преобразовывать существующие HTML-сущности. При включении приведет к преобразованию &copy; > &amp;copy;
     * @return string
     */
    static public function getHtmlEntityEncode(
        $str,
        $flags = ENT_COMPAT | ENT_HTML5,
        $encoding = 'UTF-8',
        $doubleEncode = false
    ) {
        return $return = htmlentities((string)$str, $flags, $encoding, $doubleEncode);
    }


    /**
     * Декодирование закодированой URL строки
     *
     * @param string $str      - строка, которая должна быть декодирована.
     * @param bool   $raw      - флаг для переключения метода декодирования на rawurldecode() без преобразования символа +
     * @param string $encoding - кодировка
     * @return string
     */
    static public function getUrlDecode($str = '', $raw = false, $encoding = 'UTF-8')
    {
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
     * @note RFC 3986: cтрокf, в которой все не цифро-буквенные символы, кроме -_.~,
     *       должны быть заменены знаком процента (%) за которым следует два шестнадцатеричных числа
     *
     * @param string $str      - строка, которая должна быть декодирована.
     * @param bool   $raw      - флаг для переключения метода кодирования на rawurlencode() согласно RFC 3986 без преобразования символа +
     * @param string $encoding - кодировка
     * @return string
     */
    static public function getUrlEncode($str = '', $raw = false, $encoding = 'UTF-8')
    {
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
     * Форматирует число с разделением групп
     *
     * @param mixed  $str
     * @param int    $decimals      - точность
     * @param string $separator     - разделитель точности
     * @param string $thousands_sep - разделитель тысяч
     * @param int    $default
     * @return string
     */
    static public function getNumberFormat(
        $str,
        $decimals = 2,
        $separator = '.',
        $thousands_sep = '',
        $default = 0
    ) {
        $decimals = $decimals > 0 ? $decimals : 2;
        $str = is_string($str) ? $str : strval($str);

        // Помощь при опечатках если разделители классические
        if ($separator == '.') {
            $str = Helper::str_replace_once(",", ".", $str);
        } elseif ($separator == ',') {
            $str = Helper::str_replace_once(".", ",", $str);
        }

        try {
            $return = number_format($str, $decimals, $separator, $thousands_sep);
        } catch (\Throwable $e) {
            $return = number_format($default, $decimals, $separator, $thousands_sep);
        }

        return $return;
    }


    /**
     * Создает токен по двум алгоритмам.
     *
     * @note
     * @param integer $length - минимальное значение 10
     * @param integer $split
     * @param integer $readable
     * @return string
     */
    static public function randomToken($length = 128, $split = 0, $readable = 0)
    {
        $unreadablePool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $readablePool = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $numberPool = '0123456789';
        $pool = intval($readable) === 2 ? $numberPool : ($readable ? $readablePool : $unreadablePool);

        $value = substr(str_shuffle(str_repeat($pool, (int)$length / 10)), 0, $length);

        if ($split > 0) {
            $value = join('-', str_split($value, $split));
        }

        return $value;
    }


    /**
     * Возвращает часть строки.
     *
     * @param string   $string
     * @param int      $start
     * @param int|null $length
     * @return string
     */
    static public function substr($string, $start, $length = null)
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }


    /**
     * Преобразует первый символ строки в верхний регистр
     *
     * @param string $string
     * @return string
     */
    static public function ucfirst($string)
    {
        $string = VarStr::getMakeString($string);

        return static::getUpper(static::substr($string, 0, 1)) . static::substr($string, 1);
    }


    /**
     * Убирает из строки лишние пробелы и переводы строк для последующей проверки на пустоту
     *
     * @param string $str
     * @return string
     */
    static public function getClean($str = '')
    {
        $str = VarStr::getMakeString($str);
        $search = ["\n", "\t", "\r", '&nbsp;', '&emsp;', '&ensp;', '&thinsp;'];
        $str = str_replace($search, ['', '', '', ' ', ' ', ' ', ' '], $str);
        $str = static::trim(preg_replace('/\s{2,}/', ' ', $str), "\x00..\x1F"); // убрать лишние пробелы

        return $str;
    }


    /**
     * Проверка строки на на пустое значение.
     *
     * @param string $value
     * @return bool
     */
    static public function isEmpty($value = '')
    {
        if (is_null($value) || is_bool($value) || is_array($value)) {
            return true;
        }

        if (gettype($value) !== 'string') {
            $value = VarStr::getMakeString($value);
        }

        $value = str_replace(["\n", "\t", "\r"], '', $value);
        $value = str_replace('&nbsp;', ' ', static::trim((string)$value, "\x00..\x1F"));

        return (preg_match("/(\S+)/i", $value) == 0 ? true : false);
    }


    /**
     * Удаляет пробелы из начала и конца строки (или другие символы при передачи их вторым параметром )
     *
     * @note \x0B вертикальная табуляция,
     *
     * @param string $str
     * @param string $removeChar - список символов для удаления
     * @return string
     */
    static public function trim($str = '', $removeChar = " \t\n\r\0\x0B")
    {
        if (! is_string($str)) {
            $str = VarStr::getMakeString($str);
        }

        $str = trim($str, (string)$removeChar);
        //$str = trim($str, chr(194) . chr(160)); // работает только в ASCII а иначе это &#171;

        // удаляем управляющие ASCII-символы с начала и конца $binary (от 0 до 31 включительно)
        return trim($str, "\x00..\x1F");
    }


    /**
     * Замена повторяющегося символа
     *
     * @note нужно учитывать что списки должны совпадать по длине!
     *
     * @param string         $str
     * @param string | array $char
     * @param string | array $replace
     * @return string
     */
    static public function getRemovingDoubleChar(string $str = '', $char = ' ', $replace = ' ')
    {
        $char = is_array($char) ? $char : [(string)$char];
        $replace = is_array($replace) ? $replace : [(string)$replace];

        foreach ($char as $key => $symbol) {
            if (isset($replace[$key])) {
                $str = static::trim(preg_replace("/" . preg_quote($symbol, '/') . "{2,}/", $replace[$key], $str));
            }
        }

        return $str;
    }


    /**
     * Удаление указаных символов из строки;
     *
     * @param string       $str
     * @param array|string $removeChar - список символов для удаления
     * @return string
     */
    static public function getRemoveSymbol($str = '', $removeChar = ["\n", "\r", "\t"])
    {
        $removeChar = is_array($removeChar) ? $removeChar : [VarStr::getMakeString($removeChar)];

        if (gettype($str) !== 'string') {
            $str = VarStr::getMakeString($str);
        }

        if (count($removeChar) > 0) {
            $str = str_replace($removeChar, '', $str);
        }

        return $str;
    }


    /**
     * Проверка даты под указаный формат
     *
     * @param string $date
     * @param string $format
     * @return bool
     */
    static public function validateDateTime($date = null, $format = 'Y-m-d H:i:s')
    {
        if (! is_string($date)) {
            return false;
        }

        $dt = DateTime::createFromFormat($format, $date);

        return $dt && $dt->format($format) === $date;
    }


    /**
     * Проверка строки на дату по указаному формату;
     *
     * @param string $str
     * @param mixed  $default
     * @param string $format
     * @return string|mixed
     */
    static public function makeDate($str, $default = null, $format = 'Y-m-d')
    {
        if (! is_string($str)) {
            return $default;
        }

        $str = static::crop(static::getRemoveSymbol(trim($str)));

        return VarStr::validateDateTime($str, $format) === true ? $str : $default;
    }


    /**
     * Безопасное преобразование строки в utf-8
     *
     * @param string $text
     * @return string
     */
    static public function toUTF8($text = '')
    {
        if (gettype($text) !== 'string') {
            $text = VarStr::getMakeString($text);
        }

        $encoding = mb_detect_encoding($text, mb_detect_order(), false);

        if ($encoding === "UTF-8") {
            $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        }

        return @iconv($encoding, "UTF-8//IGNORE", $text);
    }


    /**
     * Разбивает строку по разделителю и дополнительно производит удаление пустых значений;
     *
     * @param string $delimiter - разделитель
     * @param string $str       - строка
     * @param array  $deleted   - массив значений которые надо удалить
     * @return array
     */
    static public function explode($delimiter, $str, $deleted = ['', 0, null, 'null'])
    {
        if (gettype($str) !== 'string') {
            $str = VarStr::getMakeString($str);
        }

        if (! is_null($deleted)) {
            return VarArray::getRemove(explode($delimiter, static::trim($str)), $deleted);
        } else {
            return explode($delimiter, static::trim($str));
        }
    }

    /**
     * Разбивает строку по разделителю и дополнительно производит удаление пустых значений;
     *
     * @param string $delimiter - разделитель
     * @param string $str       - строка
     * @param string $action    - id|ids|number|integer
     * @return array
     */
    static public function explodeToNumber($delimiter, $str, $action = "ids")
    {
        if (gettype($str) !== 'string') {
            $str = VarStr::getMakeString($str);
        }

        $ids = explode($delimiter, static::trim($str));

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
     * @param string $str
     * @param int    $flags
     * @param string $charset = utf-8 (ISO-8859-1)
     * @return string
     * @todo это решение на php4, но пока решил оставить на всякий случай
     *
     */
    static function getDecodeEntities($str = '', $flags = ENT_COMPAT, $charset = 'UTF-8')
    {
        if (gettype($str) !== 'string') {
            $str = VarStr::getMakeString($str);
        }

        $str = preg_replace_callback('/&([a-zA-Z][a-zA-Z0-9]+);/', 'convert_entity', $str);

        return html_entity_decode($str, $flags, $charset);
    }


    /**
     * Безопасное преобразование строки в указаную кодировку если она таковой не является
     *
     * @param string $str      - строка, для которой требуется определить кодировку
     * @param string $encoding - список возможных кодировок
     * @return string
     */
    static public function getTransformToEncoding($str = '', $encoding = 'UTF-8')
    {
        if (! mb_check_encoding($str, $encoding)) {
            $str = mb_convert_encoding($str, $encoding);
            $str = @iconv(mb_detect_encoding($str, mb_detect_order(), false), "{$encoding}//IGNORE", $str);
        }

        return $str;

        /**
         * $currentEncoding = mb_detect_encoding($str, mb_detect_order(), true); // у меня было false но в документации написано надо true
         *
         * if ($currentEncoding !== $encoding) {
         * $str = mb_convert_encoding($str, $encoding);
         * $str = @iconv(mb_detect_encoding($str, mb_detect_order(), false), "{$encoding}//IGNORE", $str);
         * }
         *
         * return $str;*/
    }

    /**
     * Транскрипция текста
     *
     * @param string $word
     * @return string
     */
    static public function getTranscription(string $word): string
    {
        $chars = [
            "а" => "a",
            "к" => "k",
            "х" => "kh",
            "б" => "b",
            "л" => "l",
            "ц" => "c",
            "в" => "v",
            "м" => "m",
            "ч" => "ch",
            "г" => "g",
            "н" => "n",
            "ш" => "sh",
            "д" => "d",
            "о" => "o",
            "щ" => "sch",
            "е" => "e",
            "п" => "p",
            "ъ" => "",
            "ё" => "yo",
            "р" => "r",
            "ы" => "y",
            "ж" => "zh",
            "с" => "s",
            "ь" => "",
            "з" => "z",
            "т" => "t",
            "э" => "e",
            "и" => "i",
            "у" => "u",
            "ю" => "yu",
            "й" => "y",
            "ф" => "f",
            "я" => "ya",
        ];

        $word = mb_strtolower($word, 'UTF-8');
        $word = strip_tags($word);
        $word = preg_replace("/&[a-zA-Z]+;/u", '-', $word);
        $word = str_replace("+", ' plus', $word);
        $word = preg_replace("/[^a-zа-я0-9_]/siu", "-", $word);
        $word = strtr($word, $chars);
        $word = preg_replace("/[-]+/u", '-', $word);
        $word = trim($word, '-');
        $word = iconv("UTF-8", "UTF-8//IGNORE", $word);
        $word = mb_substr($word, 0, 255);

        return $word;
    }
}