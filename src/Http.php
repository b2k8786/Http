<?php

namespace Ak;

class Http
{
    public static $verifySSL  = false;
    public static $httpHeader = [];
    protected static $curl;

    protected static function init()
    {
        self::$curl = curl_init();
        if (count(self::$httpHeader))
            curl_setopt(self::$curl, CURLOPT_HTTPHEADER, self::$httpHeader);
        curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(self::$curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt(self::$curl, CURLOPT_AUTOREFERER, true);
        curl_setopt(self::$curl, CURLOPT_SSL_VERIFYPEER, self::$verifySSL);
    }
    /**
     * Generate a post request to given URL
     * 
     * @param string $url
     * @param array $params
     * @param callable $successCallback   
     * @param callable $errorCallback
     */
    static function post($url, $params = null, $successCallback = null, $errorCallback = null)
    {
        self::init();
        if (count(self::$httpHeader))
            curl_setopt(self::$curl, CURLOPT_HTTPHEADER, self::$httpHeader);
        curl_setopt(self::$curl, CURLOPT_URL, $url);
        curl_setopt(self::$curl, CURLOPT_POST, true);
        
        if (!empty($params))
            curl_setopt(self::$curl, CURLOPT_POSTFIELDS, $params);

        $res = curl_exec(self::$curl);
        $err = curl_error(self::$curl);
        curl_close(self::$curl);

        if (!empty($err) && is_callable($errorCallback))
            return $errorCallback($err);
        else if (is_callable($successCallback))
            return $successCallback($res);
    }

    /**
     * Generate a get request to given URL
     * 
     * @param string $url
     * @param array $params
     * @param callable $successCallback   
     * @param callable $errorCallback
     */
    static function get($url, $params = null, $successCallback = null, $errorCallback = null)
    {
        self::init();
        if (count(self::$httpHeader))
            curl_setopt(self::$curl, CURLOPT_HTTPHEADER, self::$httpHeader);
        if (empty($url))
            throw ("URL is must");

        if (!empty($params))
            $url =   $url . http_build_query($params);
        curl_setopt(self::$curl, CURLOPT_URL, $url);

        $res = curl_exec(self::$curl);
        $err = curl_error(self::$curl);
        curl_close(self::$curl);

        if (!empty($err) && is_callable($errorCallback))
            return $errorCallback($err);
        else if (is_callable($successCallback))
            return $successCallback($res);
    }

    /**
     * Generate a multipart post request with files included to given URL
     * 
     * @param string $url
     * @param array $params
     * @param array $files
     * @param callable $successCallback   
     * @param callable $errorCallback
     */
    static function multipartPost($url, $params, $files = null, $successCallback = null, $errorCallback = null)
    {
        if (!empty($files)) {
            foreach ($files as $key => $file) {
                $params[$key] = curl_file_create(
                    $file['tmp_name'],
                    $file['type'],
                    $file['name']
                );
            }
        }

        $curl = self::init();
        curl_setopt($curl, CURLOPT_URL, $url);

        self::setHeader('Content-Type', 'multipart/form-data');

        curl_setopt(self::$curl, CURLOPT_HTTPHEADER, self::$httpHeader);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

        $res = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if (!empty($err) && is_callable($errorCallback))
            return $errorCallback($err);
        else if (is_callable($successCallback))
            return $successCallback($res);
    }

    /**
     * Undocumented function
     *
     * @param string $key
     * @param string $value
     */
    static function setHeader($key, $value)
    {
        array_push(self::$httpHeader, "$key: $value");
    }
}
