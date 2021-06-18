<?php

namespace Ak;

class Http
{
    public bool $verifySSL  = false;
    public array $headers = [];
    public array $parameters = [];
    public $curl;
    private array $info;

    private function init()
    {
        $this->curl = curl_init();
        if (count($this->headers))
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, $this->verifySSL);
    }

    public function setHeader(string $key, string $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }
    public function parameter(string $key, string $value)
    {
        $this->parameters[$key] = $value;
        return $this;
    }
    public function files(array $files)
    {
        if (!empty($files)) {
            foreach ($files as $key => $file) {
                $this->parameters[$key] = curl_file_create(
                    $file['tmp_name'],
                    $file['type'],
                    $file['name']
                );
            }
        }
        return $this;
    }
    /**
     * Generate a post request to given URL
     *
     * @param string $url
     * @param callable $successCallback
     * @param callable $errorCallback
     */
    public function post(string $url, $successCallback = null, $errorCallback = null)
    {
        $this->init();
        if (count($this->headers))
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST, true);

        if (!empty($this->parameters))
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->parameters);

        $res = curl_exec($this->curl);
        $err = curl_error($this->curl);
        $this->setInfo();

        if (!empty($err) && is_callable($errorCallback))
            return $errorCallback($err);
        else if (is_callable($successCallback))
            return $successCallback($res);
    }

    /**
     * Generate a get request to given URL
     *
     * @param string $url
     * @param callable $successCallback
     * @param callable $errorCallback
     */
    public function get(string $url, $successCallback = null, $errorCallback = null)
    {
        $this->init();
        if (count($this->headers))
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
        if (empty($url))
            throw ("URL is must");

        if (!empty($this->parameters))
            $url =   $url . '?' . http_build_query($this->parameters);
        curl_setopt($this->curl, CURLOPT_URL, $url);

        $res = curl_exec($this->curl);
        $err = curl_error($this->curl);
        $this->setInfo();

        if (!empty($err) && is_callable($errorCallback))
            $errorCallback($err);
        else if (is_callable($successCallback))
            $successCallback($res);

        return $this;
    }

    /**
     * Generate a multipart post request with files included to given URL
     *
     * @param string $url
     * @param callable $successCallback
     * @param callable $errorCallback
     */
    public function multipartPost(string $url, $successCallback = null, $errorCallback = null)
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);

        $this->setHeader('Content-Type', 'multipart/form-data');

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
        curl_setopt($this->curl, CURLOPT_POST, true);
        if (count($this->parameters))
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->parameters);

        $res = curl_exec($this->curl);
        $err = curl_error($this->curl);
        $this->setInfo();

        if (!empty($err) && is_callable($errorCallback))
            return $errorCallback($err);
        else if (is_callable($successCallback))
            return $successCallback($res);
    }

    private function setInfo()
    {
        $this->info = curl_getinfo($this->curl);
    }
    public function getInfo()
    {
        return $this->info;
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }
}
