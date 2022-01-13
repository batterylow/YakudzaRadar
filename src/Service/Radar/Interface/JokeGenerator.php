<?php

namespace App\Service\Radar\Interface;

use App\Service\ImageAnalyze\Model\ImageData;

/**
 * Интерфейс генератора шуток для ответа
 */
interface JokeGenerator {

    /**
     * Выбор шутки для ответа
     *
     * @param ImageData $imageData
     * @param float $probability
     * @return string
     */
    public function getJoke(ImageData $imageData, float $probability) :string;

}