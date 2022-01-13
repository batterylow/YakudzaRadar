<?php

namespace App\Service\ImageAnalyze\Exception;

/**
 * Исключение для ошибки запроса к АПИ нейросети по анализу изображения
 */
class ImageAnalyzeResponseException extends \RuntimeException {

    const DEFAULT_MESSAGE = 'Нейросеть не ответила, у неё есть дела поважнее!';

    public function __construct($message = '', $code = 0, \Throwable $previous = null) {

        if(!$message){
            $message = self::DEFAULT_MESSAGE;
        }

        parent::__construct($message, $code, $previous);

    }
}