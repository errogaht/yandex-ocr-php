<?php

namespace Errogaht\YandexOCR;

use wapmorgan\FileTypeDetector\Detector;

class Client
{
    private $url;
    private $langFrom;
    private $langTo;
    private $filePath;
    private $fileMime;

    /**
     * Client constructor.
     * @param $url
     * @param $langFrom
     * @param $langTo
     */
    public function __construct($langFrom = 'ru', $langTo = 'en', $url = 'https://translate.yandex.net/ocr/v1.0/recognize')
    {
        $this->url = $url;
        $this->langFrom = $langFrom;
        $this->langTo = $langTo;
    }

    /**
     * @return mixed
     */
    public function getFileMime()
    {
        return $this->fileMime;
    }

    /**
     * @param mixed $fileMime
     * @return Client
     */
    public function setFileMime($fileMime)
    {
        $this->fileMime = $fileMime;
        return $this;
    }

    /**
     * @param string $url
     * @return Client
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param string $langFrom
     * @return Client
     */
    public function setLangFrom($langFrom)
    {
        $this->langFrom = $langFrom;
        return $this;
    }

    /**
     * @param string $langTo
     * @return Client
     */
    public function setLangTo($langTo)
    {
        $this->langTo = $langTo;
        return $this;
    }

    /**
     * @param mixed $filePath
     * @return Client
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
        return $this;
    }


    public function request()
    {
        if (!is_file($this->filePath)) {
            throw new \Exception('No file provided');
        }
        $ch = curl_init();
        $q = http_build_query([
            'srv' => 'tr-image',
            'lang' => "$this->langFrom,$this->langTo"
        ]);
        $url = $this->url . '?' . $q;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if (empty($this->fileMime)) {
            $type = Detector::detectByFilename($this->filePath);
            $this->fileMime = isset($type[1]) ? Detector::getMimeType($type[1]) : null;
        }

        if (empty($this->fileMime)) {
            throw new \Exception('No mime type provided or unable to get file mime type');
        }

        $filePostName = 'blob';
        $fields = [
            'file' => new \CurlFile($this->filePath, $this->fileMime, $filePostName)
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $server_output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode !== 200) {
            throw new YandexAPIErrorResponseException($server_output, curl_getinfo($ch), $this->filePath, $this->fileMime, $filePostName);
        }
        curl_close($ch);
        return json_decode($server_output, true);
    }
}