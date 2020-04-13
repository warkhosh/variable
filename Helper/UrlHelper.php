<?php

namespace Warkhosh\Variable\Helper;

/**
 * Class UrlHelper
 *
 * @package Warkhosh\Variable\Helper
 */
class UrlHelper
{
    /**
     * Возвращает порт на компьютере сервера, используемый веб-сервером для соединения.
     *
     * @return int
     */
    static public function getServerPort(): int
    {
        return $_SERVER['SERVER_PORT'];
    }

    /**
     * Возвращает имя хоста, на котором выполняется текущий скрипт
     *
     * @return string
     */
    static public function getServerName(): string
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * Возвращает протокол.
     *
     * @return string
     */
    static public function getServerProtocol()
    {
        if ((! empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
            (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
            (! empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443')) {
            return 'https';
        }

        return 'http';
    }

    /**
     * Возвращает протокол с его префиксами для домена.
     *
     * @return string
     */
    static public function getUrlScheme()
    {
        return static::getServerProtocol() . "://";
    }

    /**
     * Возвращает путь текущего запроса.
     *
     * @param bool $query - флаг для включения\выключения query параметров запроса
     * @return string
     */
    static public function getRequestUri(bool $query = true): string
    {
        static $data = ["REQUEST_URI" => null, "REQUEST_URI_WITHOUT_QUERY" => null];

        if ($query === true && ! is_null($data["REQUEST_URI"])) {
            return $data["REQUEST_URI"];

        } elseif ($query === false && ! is_null($data["REQUEST_URI_WITHOUT_QUERY"])) {
            return $data["REQUEST_URI_WITHOUT_QUERY"];
        }

        if (is_null($data["REQUEST_URI"])) {
            $data["REQUEST_URI"] = isset($_SERVER["REQUEST_URI"]) ? strval($_SERVER["REQUEST_URI"]) : "";

            // проверка и чистка повторяющихся слешей в начале урла
            if (($uri = ltrim($data["REQUEST_URI"], "/")) != $data["REQUEST_URI"]) {
                $data["REQUEST_URI"] = "/{$uri}";
            }
        }

        if ($query === false && is_null($data['REQUEST_URI_WITHOUT_QUERY'])) {
            // Обязательно прописываем протокол и сервер иначе два первых слеша будут приняты за протокол!
            $url = UrlHelper::getUrlScheme() . UrlHelper::getServerName() . $data["REQUEST_URI"];
            $data["REQUEST_URI_WITHOUT_QUERY"] = parse_url($url, PHP_URL_PATH);

            return $data["REQUEST_URI_WITHOUT_QUERY"];
        }

        return $data["REQUEST_URI"];
    }

    /**
     * Возвращает путь без файла и query параметров
     *
     * @note метод следит что-бы значения начинались со слэша
     * @param string $uri
     * @return string
     */
    static public function getMakePath(string $uri = ''): string
    {
        $uri = parse_url(rawurldecode(trim($uri)), PHP_URL_PATH);
        $info = pathinfo($uri);

        if (isset($info["extension"])) {
            $uri = $info["dirname"];

        } else {
            $info["dirname"] = isset($info["dirname"]) ? "{$info["dirname"]}/" : '';

            // Данное решение фиксит баг при обрабатке кривого урла, когда в конце get параметров идет слэш или слеши
            // @example: http://photogora.ru/background/muslin&filter_category=126/
            $tmp = "{$info["dirname"]}{$info["basename"]}";
            $uri = rtrim($uri, '/') == $tmp ? $uri : $tmp;
        }

        return Helper::start("/", $uri);
    }

    /**
     * Возвращает адрес страницы с которой пришли на страницу.
     *
     * @return string
     */
    static public function getReferer(): string
    {
        static $referer;

        if (! is_null($referer)) {
            return $referer;
        }

        $referer = key_exists("HTTP_REFERER", $_SERVER) ? $_SERVER["HTTP_REFERER"] : "";

        return $referer;
    }

    /**
     * Возвращает название агента ( браузер ) через который просматривают сайт.
     *
     * @param string $default
     * @return string
     */
    static public function getUserAgent(string $default = "undefined")
    {
        static $userAgent;

        if (! is_null($userAgent)) {
            return $userAgent;
        }

        $userAgent = key_exists("HTTP_USER_AGENT", $_SERVER) ? $_SERVER["HTTP_USER_AGENT"] : $default;

        return $userAgent;
    }

    /**
     * Возвращает строку запроса, если есть.
     *
     * @return string
     */
    static public function getQueryString(): string
    {
        static $queryString;

        if (! is_null($queryString)) {
            return $queryString;
        }

        $queryString = UrlHelper::getRequestUri(true);
        $queryString = http_build_query(static::getQueries($queryString));

        return $queryString;
    }

    /**
     * Возвращает IP посетителя
     *
     * @return string
     */
    static public function getUserIp(): string
    {
        if (array_key_exists('HTTP_CLIENT_IP', $_SERVER) && mb_strlen($_SERVER['HTTP_CLIENT_IP']) > 1) {
            return $_SERVER['HTTP_CLIENT_IP'];

        } elseif (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) &&
            mb_strlen($_SERVER['HTTP_X_FORWARDED_FOR']) > 1) {
            $tmp = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            return $tmp = array_pop($tmp);

        } elseif (array_key_exists('HTTP_X_FORWARDED', $_SERVER) && mb_strlen($_SERVER['HTTP_X_FORWARDED']) > 1) {
            return $_SERVER['HTTP_X_FORWARDED'];

        } elseif (array_key_exists('HTTP_FORWARDED_FOR', $_SERVER) && mb_strlen($_SERVER['HTTP_FORWARDED_FOR']) > 1) {
            return $_SERVER['HTTP_FORWARDED_FOR'];

        } elseif (array_key_exists('HTTP_FORWARDED', $_SERVER) && mb_strlen($_SERVER['HTTP_FORWARDED']) > 1) {
            return $_SERVER['HTTP_FORWARDED'];

        } elseif (array_key_exists('HTTP_X_REAL_IP', $_SERVER) && mb_strlen($_SERVER['HTTP_X_REAL_IP']) > 1) {
            return $_SERVER['HTTP_X_REAL_IP'];

        } elseif (array_key_exists('REMOTE_ADDR', $_SERVER) && mb_strlen($_SERVER['REMOTE_ADDR']) > 1) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return '127.0.0.1';
    }

    /**
     * Возвращает IP-адрес сервера, на котором выполняется текущий скрипт.
     *
     * @return string
     */
    static public function getServerIp(): string
    {
        static $serverAddr;

        if (! is_null($serverAddr)) {
            return $serverAddr;
        }

        $serverAddr = '127.0.0.1';

        if (array_key_exists('SERVER_ADDR', $_SERVER) && mb_strlen($_SERVER['SERVER_ADDR']) > 1) {
            $serverAddr = $_SERVER['SERVER_ADDR'];

        } elseif (array_key_exists('LOCAL_ADDR', $_SERVER) && mb_strlen($_SERVER['LOCAL_ADDR']) > 1) {
            $serverAddr = $_SERVER['LOCAL_ADDR'];
        }

        return $serverAddr;
    }

    /**
     * Возвращает информацию о файле по указанному пути.
     *
     * @note в случае не удачи вернет пустую строку.
     *
     * @param string $str
     * @return string
     */
    static public function getFile($str = ''): string
    {
        $str = parse_url($str, PHP_URL_PATH);
        $info = pathinfo($str);

        // если есть расширение файла то пытаемся отдельно установить параметры файла
        if (isset($info['extension']) &&
            isset($info['filename']) &&
            ! empty($info['extension']) &&
            ! empty($info['filename'])) {
            return "{$info['filename']}.{$info['extension']}";
        }

        return "";
    }

    /**
     * Возвращает массив query переменных из указанной строки.
     *
     * @param string $str
     * @return array
     */
    static public function getQueries(string $str): array
    {
        $str = UrlHelper::getUrlDecode($str);

        // если указали ссылку с путями то выбираем из неё только query параметры
        if (mb_substr($str, 0, 1) === '/' || mb_substr($str, 0, 4) === 'http') {
            $str = ! is_null($tmp = parse_url($str, PHP_URL_QUERY)) ? $tmp : '';
        }

        parse_str(Helper::getRemoveStart("?", $str), $queries);

        return $queries;
    }

    /**
     * Возвращает название используемого метода для запроса текущей страницы.
     *
     * @return string
     */
    static public function getRequestMethod(): string
    {
        static $requestMethod;

        if (! is_null($requestMethod)) {
            return $requestMethod;
        }

        $requestMethod = 'get';

        if (array_key_exists('REQUEST_METHOD', $_SERVER)) {
            $requestMethod = Helper::getLower($_SERVER['REQUEST_METHOD']);

            // По общему тренду поддерживаю передачу POST данных с переменной _method
            if ($requestMethod === 'post' && isset($_POST['_method']) && $_POST['_method'] != '') {
                $_POST['_method'] = Helper::getLower(trim($_POST['_method']));

                if (in_array($_POST['_method'], ['put', 'patch', 'delete'])) {
                    $requestMethod = $_POST['_method'];
                }
            }

            return $requestMethod;
        }

        return $requestMethod;
    }

    /**
     * Генератор урлов.
     *
     * @param array $parts
     * @return string
     */
    static public function getGenerated(array $parts = []): string
    {
        $scheme = isset($parts['scheme']) ? Helper::ending("://", $parts['scheme']) : 'http://';
        $host = isset($parts['host']) ? $parts['host'] : '';
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
        $user = isset($parts['user']) ? $parts['user'] : '';
        $pass = isset($parts['pass']) ? ':' . $parts['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';

        if (empty($host)) {
            $scheme = $user = $pass = $host = $port = '';
        }

        $server_name = isset($parts['server_name']) ? $parts['server_name'] : $scheme . $user . $pass . $host . $port;
        $path = isset($parts['path']) ? $parts['path'] : '';

        if (isset($parts['queries']) && is_array($parts['queries'])) {
            $query = count($parts['queries']) ? "?" . http_build_query($parts['queries']) : '';
        } else {
            $query = isset($parts['query']) && ! empty($parts['query']) ? Helper::start("?", $parts['query']) : '';
        }

        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

        return "{$server_name}{$path}{$query}{$fragment}";
    }

    /**
     * Декодирование закодированой URL строки.
     *
     * @param string $str      - строка, которая должна быть декодирована.
     * @param bool   $raw      - флаг для переключения метода декодирования на rawurldecode() без преобразования символа +
     * @param string $encoding - кодировка
     * @return string
     */
    static public function getUrlDecode(string $str = '', bool $raw = false, string $encoding = 'UTF-8'): string
    {
        $str = Helper::getTransformToEncoding((string)$str, $encoding);

        if ($raw) {
            return rawurldecode($str); // раскодирует контент по RFC 3986
        }

        // стараемся сохранить символ плюса
        foreach ($parts = explode("+", $str) as $key => $text) {
            // раскодирует контент по типу application/x-www-form-urlencoded где пробел это +
            $parts[$key] = urldecode($text);
        }

        return join("+", $parts);
    }

    /**
     * Кодирование строки для URL.
     *
     * @note RFC 3986: строк, в которой все не цифро-буквенные символы, кроме -_.~,
     *       должны быть заменены знаком процента (%) за которым следует два шестнадцатеричных числа
     *
     * @param string $str      - строка, которая должна быть декодирована.
     * @param bool   $raw      - флаг для переключения метода кодирования на rawurlencode() согласно RFC 3986 без преобразования символа +
     * @param string $encoding - кодировка
     * @return string
     */
    static public function getUrlEncode(string $str = '', bool $raw = false, string $encoding = 'UTF-8'): string
    {
        $str = Helper::getTransformToEncoding((string)$str, $encoding);

        if ($raw) {
            return rawurlencode($str); // кодирует строку по RFC 3986
        }

        // Возвращает строку, в которой все не цифро-буквенные символы, кроме -_. должны быть заменены знаком процента (%),
        // за которым следует два шестнадцатеричных числа, а пробелы закодированы как знак сложения (+).
        // Строка кодируется тем же способом, что и POST-данные веб-формы, то есть по типу контента application/x-www-form-urlencoded
        return urlencode($str);
    }

    /**
     * Разбивает строку по разделителю и дополнительно производит удаление пустых значений.
     *
     * @param string $delimiter - разделитель
     * @param string $str       - строка
     * @param array  $deleted   - массив значений которые надо удалить
     * @return array
     */
    static public function explode(string $delimiter = ',', string $str = '', array $deleted = [''])
    {
        if (! is_null($deleted)) {
            return array_diff(explode($delimiter, trim($str)), $deleted);

        } else {
            return explode($delimiter, Helper::trim($str));
        }
    }

    /**
     * Преобразует HTML-сущности в специальные символы через регулярное выражение и список сущностей.
     * Преобразует &amp; > & | &quot; > " | &bull; > •
     *
     * @param string $str
     * @param int    $flags
     * @param string $charset = utf-8 (ISO-8859-1)
     * @return string
     *
     * @todo это решение на php4, но пока решил оставить на всякий случай
     *
     */
    static function getDecodeEntities(string $str = '', int $flags = ENT_COMPAT, string $charset = 'UTF-8'): string
    {
        $str = preg_replace_callback('/&([a-zA-Z][a-zA-Z0-9]+);/', 'convert_entity', $str);

        return html_entity_decode($str, $flags, $charset);
    }

    /**
     * Модифицирует переданный список query значений
     *
     * @param array $insert  - список значений для добавления к query параметрам
     * @param array $remove  - список значений для удаления из query параметров
     * @param array $queries - список query параметров
     * @return array
     */
//    public function getModifiedQueryList(array $insert = [], array $remove = [], array $queries = []): array
//    {
//        $queries = VarArray::getItemsExtract($remove, $queries);
//
//        foreach ($insert as $key => $value) {
//            $queries[$key] = $value;
//        }
//
//        return $queries;
//    }

    /**
     * Формирует список query значений из referer query от переданных параметров
     *
     * @param array $insert - список значений для добавления к query параметрам
     * @param array $remove - список значений для удаления из query параметров
     * @return array
     */
//    static public function getModifyQueryInReferer($insert = [], $remove = [])
//    {
//        $queries = static::getInstance()->referer_queries;
//        $queries = VarArray::getItemsExtract($remove, $queries);
//
//        foreach ($insert as $key => $value) {
//            $queries[$key] = $value;
//        }
//
//        return $queries;
//    }

    /**
     * Формирует список query значений в зависимости от переданных параметров
     *
     * @param array $insert - список значений для добавления к query параметрам
     * @param array $remove - список значений для удаления из query параметров
     * @return array
     */
//    static public function getModifyQueryInRequest($insert = [], $remove = [])
//    {
//        $queries = static::getInstance()->referer_queries;
//        $queries = VarArray::getItemsExtract($remove, $queries);
//
//        foreach ($insert as $key => $value) {
//            $queries[$key] = $value;
//        }
//
//        return $queries;
//    }
}