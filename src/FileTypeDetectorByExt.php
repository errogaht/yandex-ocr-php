<?php

namespace Errogaht\YandexOCR;

class FileTypeDetectorByExt
{

    const IMAGE = 'image';

    const JPEG = 'jpeg';
    const BMP = 'bmp';
    const GIF = 'gif';
    const PNG = 'png';
    const TIFF = 'tiff';
    const PSD = 'psd';


    static protected $aliases = array(
        'jpg' => self::JPEG,
        'tif' => self::TIFF,
    );

    static protected $types = array(
        'jpeg' => array(self::IMAGE, self::JPEG),
        'bmp' => array(self::IMAGE, self::BMP),
        'gif' => array(self::IMAGE, self::GIF),
        'png' => array(self::IMAGE, self::PNG),
        'tiff' => array(self::IMAGE, self::TIFF),
        'psd' => array(self::IMAGE, self::PSD),
    );

    static protected $mimeTypes = array(
        self::JPEG => 'image/jpeg',
        self::BMP => 'image/bmp',
        self::GIF => 'image/gif',
        self::PNG => 'image/png',
        self::TIFF => 'image/tiff',
        self::PSD => 'image/vnd.adobe.photoshop',
    );

    static public function detectMimeByFilename($filename)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (isset(self::$aliases[$ext])) $ext = self::$aliases[$ext];
        if (isset(self::$mimeTypes[$ext])) return self::$mimeTypes[$ext];
        return null;
    }
}