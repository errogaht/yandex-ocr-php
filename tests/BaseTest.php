<?php

class BaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        // can pass optional arguments: $langFrom, $langTo, API $url
        $client = new \Errogaht\YandexOCR\Client();

        //tell path to recognizing file
        $client->setFilePath(__DIR__ . DIRECTORY_SEPARATOR . '1e741d3b-ba67-42e5-8229-f07a72072c96.png');

        //get response from Yandex translate
        $response = $client->request();
        $this->assertNotEmpty($response);

        //convert response to plain text
        $texter = new \Errogaht\YandexOCR\Response2Text($response);
        $text = $texter->getText();
        $this->assertNotEmpty($text);

        //ensure that recognize pass
        $this->assertNotFalse(strpos($text, 'написано'));

    }
}