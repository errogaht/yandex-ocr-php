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

        curl_setopt($ch, CURLOPT_URL, $this->url . '?' . $q);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if (empty($this->fileMime)) {
            $type = Detector::detectByContent($this->filePath);
            if (is_array($type) && count($type) === 2) {

            }
            $this->fileMime = implode('/', $type);
        }

        if (empty($this->fileMime)) {
            throw new \Exception('No mime type provided or unable to get file mime type');
        }

        $fields = [
            'file' => new \CurlFile($this->filePath, $this->fileMime, 'blob')
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpcode !== 200) {
            throw new \ErrorException($server_output);
        }
        return json_decode($server_output, true);
    }
}