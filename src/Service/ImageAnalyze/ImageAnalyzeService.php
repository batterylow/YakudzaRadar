<?php

namespace App\Service\ImageAnalyze;

use App\Service\ImageAnalyze\Interface\ImageAnalyzeProviderInterface;

/**
 * Сервис для анализа изображения
 */
class ImageAnalyzeService {

    public function __construct(
       public ImageAnalyzeProviderInterface $provider
    ) {}

}