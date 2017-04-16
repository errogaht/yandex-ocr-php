# yandex-ocr-php
Unofficial PHP package to get results from Yandex translate OCR


Features:
- Upload image to Yandex Translate OCR and get response in PHP array
- Convert response to plain text
- Unit test

## Install

`composer require errogaht/yandex-ocr-php`

## Usage

You can see test file:

```php
// can pass optional arguments: $langFrom, $langTo, API $url
$client = new \Errogaht\YandexOCR\Client();

//tell path to recognizing file
$client->setFilePath(__DIR__ . '/1e741d3b-ba67-42e5-8229-f07a72072c96.png');

//get response from Yandex translate
$response = $client->request();

//convert response to plain text
$texter = new \Errogaht\YandexOCR\Response2Text($response);
$text = $texter->getText();
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.