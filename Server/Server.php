<?php

namespace Warkhosh\Variable\Server;

use Warkhosh\Variable\Helper\ServerHelper;
use Warkhosh\Variable\Helper\UrlHelper;
use Warkhosh\Variable\Traits\Singleton;

/**
 * Class Server
 *
 * Класс для работы с переменной $_SERVER с раширенным функционалом по её значениям
 *
 * @property array   referer_queries    - список query параметров из referer!
 * @property string  referer_query      - строка с query параметрами из referer!
 * @property string  referer_path       - путь из referer безй файла и query параметров!
 * @property string  referer_file       - только название файла ( если есть ) из referer!
 * @property string  referer_uri        - пути и query параметрами из referer без протокола и домена!
 * @property array   request_queries    - список query параметров текущего запроса!
 * @property string  request_query      - значение query параметров у текущего запроса!
 * @property string  request_path       - только путь текущего запроса без файла и query параметров!
 * @property string  request_first_path - только первая папка из пути у текущего запроса!
 * @property array   request_paths      - список папок у текущего пути в запросе!
 * @property string  request_file       - только название файла ( если есть ) из текущего запроса!
 * @property string  request_uri        - путь с query параметрами текущего запроса
 * @property string  user_agent
 * @property boolean has_user_agent     - флаг наличия агента в запросе к нам
 * @property string  referer            - путь + файл + query параметры из referer
 * @property string  client_ip
 * @property string  server_ip
 * @property string  remote_addr
 * @property string  request_scheme
 * @property string  protocol
 * @property string  name
 * @property string  port
 * @property string  method
 * @property string  request_method
 * @property integer http_response_code - код ответа HTTP
 */
class Server
{
    use Singleton;

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        $permitted = [
            'referer_queries',
            'referer_query',
            'referer_path',
            'referer_file',
            'referer_uri',
            'request_queries',
            'request_query',
            'request_uri',
            'request_path',
            'request_first_path',
            'request_paths',
            'request_file',
            'user_agent',
            'has_user_agent',
            'referer',
            'client_ip',
            'server_ip',
            'remote_addr',
            'method',
            'request_method',
            'request_scheme',
            'protocol',
            'name',
            'port',
            'http_response_code',
        ];

        if (! in_array($name, $permitted)) {
            return null;
        }

        if ($name === 'http_response_code') {
            // не пишем в переменную http_response_code значение, ведь код может измениться от сценария!
            return ServerHelper::httpResponseCode();
        }

        if ($name === 'referer_queries') {
            $this->referer_queries = UrlHelper::getQueries($this->referer);

            return $this->referer_queries;
        }

        if ($name === 'referer_query') {
            $this->referer_queries = UrlHelper::getQueries($this->referer);
            $this->referer_query = '';

            if (count($this->referer_queries)) {
                $this->referer_query = "?" . http_build_query($this->referer_queries);
            }

            return $this->referer_query;
        }

        if ($name === 'referer_path') {
            $this->referer_path = ! empty($this->referer) ? UrlHelper::getMakePath($this->referer) : '';

            return $this->referer_path;
        }

        if ($name === 'referer_file') {
            $this->referer_file = UrlHelper::getFile($this->referer);

            return $this->referer_file;
        }

        if ($name === 'referer_uri') {
            $uri = '';

            if ($this->referer_path != '') {
                $uri = $this->referer_path;
                $uri .= ($this->referer_file != '' && mb_substr($uri, -1) !== '/' ? "/" : '') . $this->referer_file;
                $uri .= $this->referer_query;
            }

            $this->referer_uri = $uri;

            return $this->referer_uri;
        }

        if ($name === 'request_queries') {
            $this->request_queries = UrlHelper::getQueries(UrlHelper::getRequestUri(true));

            return $this->request_queries;
        }

        if ($name === 'request_query') {
            $this->request_query = '';

            if (count($this->request_queries)) {
                $this->request_query = "?" . http_build_query($this->request_queries);
            }

            return $this->request_query;
        }

        if ($name === 'request_path') {
            $this->request_path = UrlHelper::getMakePath(UrlHelper::getRequestUri(false));

            return $this->request_path;
        }

        if ($name === 'request_paths') {
            $this->request_path = UrlHelper::getMakePath(UrlHelper::getRequestUri(false));
            $this->request_paths = array_values(UrlHelper::explode("/", $this->request_path));

            return $this->request_paths;
        }

        if ($name === 'request_first_path') {
            $paths = $this->request_paths;
            $this->request_first_path = strval(array_shift($paths));

            return $this->request_first_path;
        }

        if ($name === 'request_file') {
            $this->request_file = UrlHelper::getFile(UrlHelper::getRequestUri(false));

            return $this->request_file;
        }

        if ($name === 'request_uri') {
            $uri = '';

            if ($this->request_path != '') {
                $uri = UrlHelper::getRequestUri(false) . $this->request_query;
            }

            $this->request_uri = $uri;

            return $this->request_uri;
        }

        if ($name === 'request_scheme' || $name === 'protocol') {
            $this->request_scheme = $this->protocol = UrlHelper::getUrlScheme();

            return $this->protocol;
        }

        if ($name === 'port') {
            $this->port = UrlHelper::getServerPort();

            return $this->port;
        }

        if ($name === 'name') {
            $this->name = UrlHelper::getServerName();

            return $this->name;
        }

        if ($name === 'has_user_agent') {
            $this->user_agent = UrlHelper::getUserAgent();

            return ($this->user_agent !== 'undefined');
        }

        if ($name === 'user_agent') {
            $this->user_agent = UrlHelper::getUserAgent();

            return $this->user_agent;
        }

        if ($name === 'referer') {
            $this->referer = UrlHelper::getReferer();

            return $this->referer;
        }

        if ($name === 'client_ip' || $name === 'remote_addr') {
            $this->client_ip = $this->remote_addr = UrlHelper::getUserIp();

            return $this->client_ip;
        }

        if ($name === 'server_ip') {
            $this->server_ip = UrlHelper::getServerIp();

            return $this->server_ip;
        }

        if ($name === 'method' || $name === 'request_method') {
            $this->method = $this->request_method = UrlHelper::getRequestMethod();

            return $this->request_method;
        }

        trigger_error("Invalid variable specified", E_USER_ERROR);
        return "";
    }

    /**
     * Проверка наличия query значений в параметрах referer ( прошлого ) запроса
     *
     * @param string|array $name
     * @return bool
     */
    public function hasQueryInReferer($name): bool
    {
        if (is_string($name) && in_array($name, $this->referer_queries)) {
            return true;

        } elseif (is_array($name) && count($name) > 0) {
            $is_assoc = array_keys($name) !== range(0, count($name) - 1);

            foreach ($name as $key => $value) {
                if ($is_assoc) {
                    $result = (key_exists($key, $this->referer_queries) && $this->referer_queries[$key] === $value);
                } else {
                    $result = in_array($value, $this->referer_queries);
                }

                if ($result !== true) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Проверка наличия query значений в текущем запросе
     *
     * @param string|array $name
     * @return bool
     */
    public function hasQueryInRequest($name = null): bool
    {
        if (is_string($name) && array_key_exists($name, $this->request_queries)) {
            return true;

        } elseif (is_array($name) && count($name)) {
            $is_assoc = array_keys($name) !== range(0, count($name) - 1);

            foreach ($name as $key => $value) {
                if ($is_assoc) {
                    $result = (key_exists($key, $this->request_queries) && $this->request_queries[$key] === $value);
                } else {
                    $result = in_array($value, $this->referer_queries);
                }

                if ($result !== true) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Возвращает название неустановленного юзер агента
     *
     * @return string
     */
    public function getUnspecifiedUserAgent(): string
    {
        return "User agent not defined (undefined)";
    }
}