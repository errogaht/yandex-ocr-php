<?php

namespace Errogaht\YandexOCR;


class Response2Text
{
    private $response;

    /**
     * Response2Text constructor.
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    public function getText()
    {
        if (!(isset($this->response['data']['blocks']) && is_array($this->response['data']['blocks']))) {
            throw new \Exception('Unable to parse respose');
        }

        return implode(' ', iterator_to_array($this->textPieces($this->response['data']['blocks'])));
    }


    private function textPieces($blocks)
    {
        foreach ($blocks as $block) {
            foreach ($block['boxes'] as $box) {
                yield $box['text'];
            }
        }
    }
}