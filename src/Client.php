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
            'lang' => "$this->langFrom,$this->langTo",
            'sid' => $this->getSid()
        ]);
        $url = $this->url . '?' . $q;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if (empty($this->fileMime)) {
            $this->fileMime = FileTypeDetectorByExt::detectMimeByFilename($this->filePath);
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

    /**
     * Yandex add sid param to prevent this script working
     * This method return sid to make ocr request
     * @throws \Errogaht\YandexOCR\UnableToGetYandexSessionIdException
     */
    private function getSid()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://translate.yandex.com/ocr');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $server_output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode !== 200) {
            throw new YandexAPIErrorResponseException($server_output, curl_getinfo($ch));
        }
        curl_close($ch);
        preg_match('~\(this, this\.document, (\{.*\}), this\.yandexTranslate\);~s', $server_output, $matches);
        if (!empty($matches[1])) {
            $matches = preg_replace('~^\s+~m', '', $matches[1]);
            preg_match("~SID: '([\\w\.-_]+)',~s", $matches, $matches);
            if (isset($matches[1])) {
                return $matches[1];
            }
        }
        throw new UnableToGetYandexSessionIdException();
    }
}