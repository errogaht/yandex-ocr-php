<?php

namespace Errogaht\YandexOCR;


class YandexAPIErrorResponseException extends \Exception
{
    public function __construct($server_output, $curlInfo, $file = null, $mime = null, $postname = null)
    {
        $msg = "Yandex say: $server_output \n File: $file\n Mime: $mime\n PostName: $postname\n Curl do:\n" . json_encode($curlInfo, JSON_PRETTY_PRINT);
        parent::__construct($msg);
    }
}