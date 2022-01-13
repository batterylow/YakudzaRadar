<?php

namespace App\Service\Radar\Model;

/**
 * Модель с результатом анализа
 */
class AnalyzeResult {

    /**
     * Сообщение с результатом анализа
     *
     * @var string
     */
    public readonly string $message;

    /**
     * Вероятность корректности результата анализа
     *
     * @var float
     */
    public readonly float $probability;


    public function __construct(string $message, float $probability) {

        $this->message = $message;
        $this->probability = $probability;

    }

}