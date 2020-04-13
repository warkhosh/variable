<?php

declare(strict_types=1);

namespace Warkhosh\Variable\Helper;

/**
 * Class Helper
 *
 * @package Warkhosh\Variable\Helper
 */
class Helper
{
    /**
     * Удаляет пробелы из начала и конца строки (или другие символы при передачи их вторым параметром).
     *
     * @note \x0B вертикальная табуляция,
     *
     * @param string $str
     * @param string $removeChar - список символов для удаления
     * @return string
     */
    static public function trim(string $str = '', string $removeChar = " \t\n\r\0\x0B"): string
    {
        $str = trim($str, (string)$removeChar);

        // удаляем управляющие ASCII-символы с начала и конца $binary (от 0 до 31 включительно)
        return trim($str, "\x00..\x1F");
    }

    /**
     * Убирает из строки лишние пробелы и переводы строк для последующей проверки на пустоту.
     *
     * @param string $str
     * @return string
     */
    static public function getClean(string $str): string
    {
        $str = str_replace(["\n", "\t", "\r", '&nbsp;'], ['', '', '', ' '], $str);
        $str = static::trim(preg_replace('/\s{2,}/', ' ', $str), "\x00..\x1F"); // убрать лишние пробелы

        return $str;
    }

    /**
     * Преобразует первый символ строки в верхний регистр.
     *
     * @param string $string
     * @return string
     */
    static public function ucfirst(string $string): string
    {
        return static::getUpper(static::substr($string, 0, 1)) . static::substr($string, 1);
    }

    /**
     * Возвращает часть строки.
     *
     * @param string   $string
     * @param int      $start
     * @param int|null $length
     * @return string
     */
    static public function substr(string $string, int $start, ?int $length = null): string
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * @param string $str
     * @return string|null
     */
    static public function getEncoding(string $str): ?string
    {
        $cp_list = ['utf-8', 'windows-1251'];
        $encoding = mb_detect_encoding($str, mb_detect_order(), false);
        $clean_str = $str;

        if ($encoding === "UTF-8") {
            $clean_str = mb_convert_encoding($str, 'UTF-8');
        }

        foreach ($cp_list as $k => $codePage) {
            if (md5($str) === @md5(@iconv($codePage, $codePage . '//IGNORE', $clean_str))) {
                return $codePage;
            }
        }

        return null;
    }

    /**
     * Безопасное преобразование строки в utf-8
     *
     * @param string $text
     * @return string
     */
    static public function toUTF8(string $text): string
    {
        $encoding = mb_detect_encoding($text, mb_detect_order(), false);

        if ($encoding === "UTF-8") {
            $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        }

        return @iconv($encoding, "UTF-8//IGNORE", $text);
    }

    /**
     * Безопасное преобразование строки в указаную кодировку если она таковой не является.
     *
     * @param string $str      - строка, для которой требуется определить кодировку
     * @param string $encoding - список возможных кодировок
     * @return string
     */
    static public function getTransformToEncoding(string $str = '', string $encoding = 'UTF-8'): string
    {
        if (! mb_check_encoding($str, $encoding)) {
            $str = mb_convert_encoding($str, $encoding);
            $str = @iconv(mb_detect_encoding($str, mb_detect_order(), false), "{$encoding}//IGNORE", $str);
        }

        return $str;
    }

    /**
     * Удаление указаных символов из строки.
     *
     * @param string       $str
     * @param array|string $removeChar - список символов для удаления
     * @return string
     */
    static public function getRemoveSymbol(string $str = '', $removeChar = ["\n", "\r", "\t"]): string
    {
        $removeChar = is_array($removeChar) ? $removeChar : [strval($removeChar)];

        if (count($removeChar) > 0) {
            $str = str_replace($removeChar, '', $str);
        }

        return $str;
    }

    /**
     * Определить, является ли данная строка начинается с определенной подстроки.
     *
     * @param string       $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function startsWith(string $haystack, $needles): bool
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
    public static function endsWith(string $haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if ((string)$needle === mb_substr($haystack, -mb_strlen($needle))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Регистрозависимый поиск первого вхождения символа в строке с возвратом номера позиции символа или false.
     *
     * @note Первый символ стоит на позиции 0, позиция второго 1 и так далее.
     *
     * @param string|array $needles - строка, поиск которой производится в строке $str
     * @param string       $str     - строка в которой ищем $needles
     * @param integer      $offset
     * @return integer|bool
     */
    public static function findPos($needles = null, string $str = '', int $offset = 0)
    {
        foreach ((array)$needles as $needle) {
            if (($pos = mb_strpos($str, $needle, $offset, 'UTF-8')) !== false) {
                return $pos;
            }
        }

        return false;
    }

    /**
     * Регистрозависимый поиск первого вхождения символа в строке с возвратом результата.
     *
     * @note Первый символ стоит на позиции 0, позиция второго 1 и так далее.
     *
     * @param string|array $needles - строка, поиск которой производится в строке $str
     * @param string       $str     - строка в которой ищем $needles
     * @param int          $offset
     * @return bool
     */
    public static function find($needles = null, string $str = '', int $offset = 0): bool
    {
        if (static::findPos($needles, $str, $offset) !== false) {
            return true;
        }

        return false;
    }

    /**
     * Замена первого вхождения.
     *
     * @param string $search
     * @param string $replace
     * @param string $text
     * @return string
     */
    static public function replaceOnce(string $search = '', string $replace = '', string $text = ''): string
    {
        if (gettype($search) !== 'string') {
            $search = strval($search);
        }

        if (gettype($replace) !== 'string') {
            $replace = strval($replace);
        }

        if (gettype($text) !== 'string') {
            $text = strval($text);
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
    public static function length(string $value): int
    {
        return mb_strlen($value);
    }

    /**
     * Устанавливает начало строки на указанное с проверкой на наличие такого значения.
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
     * Закрывает строку заданным значением с проверкой на наличие такого значения.
     *
     * @param string $prefix
     * @param string $str
     * @return string
     */
    public static function ending(string $prefix, ?string $str): string
    {
        return preg_replace("/(?:" . preg_quote($prefix, '/') . ")+\$/u", '', $str) . $prefix;
    }

    /**
     * Убирает указное значения из начала строки.
     *
     * @param string $prefix
     * @param string $str
     * @return string
     */
    static public function getRemoveStart(string $prefix, string $str): string
    {
        return preg_replace('/^(?:' . preg_quote($prefix, '/') . ')+/u', '', $str);
    }

    /**
     * Убирает указное значения из конца строки.
     *
     * @param string $prefix
     * @param string $str
     * @return string
     */
    static public function getRemoveEnding(string $prefix, string $str): string
    {
        return preg_replace('/(?:' . preg_quote($prefix, '/') . ')+$/u', '', $str);
    }

    /**
     * Убирает указное значения из начала строки.
     *
     * @param string $search
     * @param string $replace
     * @param string $str
     * @return string
     */
    static public function getReplaceStart(string $search, string $replace, string $str): string
    {
        return preg_replace('/^(?:' . preg_quote($search, '/') . ')+/u', '', $str);
    }

    /**
     * Убирает указное значения из конца строки
     *
     * @param string $search
     * @param string $replace
     * @param string $str
     * @return string
     */
    static public function getReplaceEnding(string $search, string $replace, string $str): string
    {
        return preg_replace('/(?:' . preg_quote($search, '/') . ')+$/u', $replace, $str);
    }

    /**
     * Кодирует данные в формат MIME base64
     *
     * @param string $str
     * @return string
     */
    static function getBase64UrlEncode(string $str): string
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

    /**
     * Декодирует данные, закодированные MIME base64
     *
     * @param string $str
     * @return string
     */
    static function getBase64UrlDecode(string $str): string
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

        $words = strval($words);
        $text = "";

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

        $parts = explode("+", $words);

        foreach ($parts as $key => $row) {
            $parts[$key] = urldecode($row);
        }

        $words = join("+", $parts);

        if (static::getEncoding($words) == 'windows-1251') {
            $words = iconv('CP1251', 'UTF-8', $words);
        }

        $words = mb_strtolower($words);
        $words = preg_split('//u', $words, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($words as $key => $row) {
            $text .= isset($replaceChar[$row]) ? $replaceChar[$row] : $row;
        }

        return $text;
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

        $words = strval($words);
        $text = "";

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

        $parts = explode("+", $words);

        foreach ($parts as $key => $row) {
            $parts[$key] = urldecode($row);
        }

        $words = join("+", $parts);

        if (static::getEncoding($words) == 'windows-1251') {
            $words = iconv('CP1251', 'UTF-8', $words);
        }

        $words = mb_strtoupper($words, 'UTF-8');
        $words = preg_split('//u', $words, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($words as $key => $row) {
            $text .= isset($replaceChar[$row]) ? $replaceChar[$row] : $row;
        }

        return $text;
    }

    /**
     * Ограничить количество слов в строке.
     *
     * @param string $value
     * @param int    $words
     * @param string $end
     * @return string
     */
    static public function words($value, $words = 100, $end = '...'): string
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . preg_quote($words, '/') . '}/u', $value, $matches);

        if (! isset($matches[0]) || strlen($value) === strlen($matches[0])) {
            return $value;
        }

        return rtrim($matches[0]) . $end;
    }

    /**
     * Обрезает строку до указаных символов.
     *
     * @param string   $str
     * @param null|int $length
     *
     * @return string
     */
    static public function crop(string $str = '', ?int $length = 250): string
    {
        if (is_null($length)) {
            return $str;
        }

        $length = $length > 0 ? $length : 250;
        $default = static::trim($str);

        if (is_string($str) && mb_strlen($str) > 1) {
            $default = mb_substr($default, 0, $length);
            $default = is_bool($default) ? '' : $default;
        }

        return $default;
    }

    /**
     * Сокращает текст по параметрам.
     *
     * @param string    $str
     * @param null|int  $length
     * @param string    $end
     * @param bool|true $transform - преобразование кодов в символы и обратно ( дял подсчета длинны по символам )
     * @param bool|true $smart     - флаг включающий умную систему усечения строки с учётом целостности слов
     * @return string
     */
    static public function reduce(
        string $str = '',
        ?int $length = 250,
        string $end = '',
        bool $transform = true,
        bool $smart = true
    ): string {
        if (is_null($length)) {
            return $str;
        }

        $str = static::trim($str);

        $length = $length > 0 ? intval($length) : 250;

        // вычисляем длинну текста с учетом количества символов от переменной обрезания
        $maxLength = $length - mb_strlen($end);
        $end = mb_strlen($str) > $maxLength ? $end : '';

        // Защита от человеческого фактора когда длинна строки меньше чем окончание
        if ($maxLength < 1) {
            return '';
        }

        // Кодирует коды HTML-сущностей в символы для более точного усечения
        if ($transform) {
            $str = static::getHtmlEntityDecode($str);
        }

        // Жёсткое обрезание текста, строго по лимиту
        if (! $smart) {
            $str = static::crop($str, $length) . $end;

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
    public static function getStudly(string $value): string
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
     * Преобразование Camel case в Snake case.
     *
     * @param string $input
     * @return string
     */
    static public function getSnakeCase(string $input): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];

        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }

    /**
     * Преобразование Snake case в Camel case.
     *
     * @param string $str
     * @return string
     */
    static public function getCamelCase(string $str): string
    {
        $parts = explode("_", $str);

        foreach ($parts as $key => $text) {
            $parts[$key] = static::ucfirst($text);
        }

        return join("", $parts);
    }

    /**
     * Преобразует HTML-сущности ( Entity ) в специальные символы.
     *
     * @example: &amp;copy; > &copy; | &amp; > & | &quot; > " | &bull; > •
     *
     * @param string $str
     * @param int    $flags    - битовая маска из флагов определяющая режим обработки
     * @param string $encoding - кодировка
     * @return string
     */
    static public function getHtmlEntityDecode(
        string $str,
        int $flags = ENT_COMPAT | ENT_HTML5,
        string $encoding = 'UTF-8'
    ): string {
        return html_entity_decode((string)$str, $flags, $encoding);
    }

    /**
     * Кодирует только специальные символы в их HTML-сущности.
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
        string $str,
        int $flags = ENT_COMPAT | ENT_HTML5,
        string $encoding = 'UTF-8',
        bool $doubleEncode = false
    ): string {
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
        string $str,
        int $flags = ENT_COMPAT | ENT_HTML5,
        string $encoding = 'UTF-8',
        bool $doubleEncode = false
    ): string {
        return $return = htmlentities((string)$str, $flags, $encoding, $doubleEncode);
    }

    /**
     * Проверка истинности значения;
     *
     * @param null $var
     * @param bool $strict
     * @return bool
     */
    static public function isTrue($var = null, $strict = false)
    {
        if ($var === true) {
            return true;
        }

        if (is_array($var) || is_object($var)) {
            return false;
        }

        if ($strict === false) {
            if ((int)$var === 1 || mb_strtolower(trim($var)) === 'true') {
                return true;
            }
        }

        return false;
    }

    /**
     * Проверка истинности значения;
     *
     * @param null $var
     * @param bool $strict
     * @return bool
     */
    static public function isFalse($var = null, $strict = false)
    {
        if ($var === false) {
            return true;
        }

        if (is_array($var) || is_object($var)) {
            return false;
        }

        if ($strict === false) {
            if (((int)$var === 0 || mb_strtolower(trim($var)) === 'false')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Замена первой найденной строки.
     *
     * @param string | array $search
     * @param string | array $replace
     * @param string         $text
     * @return string
     */
    static public function str_replace_once($search, $replace, string $text): string
    {
        if (is_string($search)) {
            $pos = mb_strpos($text, $search);

            if ($pos !== false) {
                // return substr_replace($text, (string)$replace, $pos, strlen($search)); шалит!
                return mb_substr($text, 0, $pos) . (string)$replace . mb_substr($text, $pos + mb_strlen($search));
            }

            return $text;
        }

        if (is_array($search) && is_bool($replace_is_string = is_string($replace))) {
            foreach ($search as $key => $searchText) {
                $replaceText = $replace_is_string ? $replace : (isset($replace[$key]) ? $replace[$key] : '');
                $text = static::str_replace_once($searchText, $replaceText, $text);
            }

            return $text;
        }

        trigger_error("invalid parameters passed");

        return $text;
    }
}