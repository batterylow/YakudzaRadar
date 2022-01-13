<?php

namespace App\Service\ImageAnalyze\Interface;

use App\Service\ImageAnalyze\Model\ImageData;

/**
 * Интерфейс провайдера для АПИ по анализу изображения
 */
interface ImageAnalyzeProviderInterface {

    /**
     * Получение данных изображения
     *
     * @param string $image Путь или URL к изображению для анализа
     * @return ImageData
     */
    public function getImageData(string $image) :ImageData;

}