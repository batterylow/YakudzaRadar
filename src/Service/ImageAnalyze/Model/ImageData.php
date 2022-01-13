<?php

namespace App\Service\ImageAnalyze\Model;

/**
 * Модель данных изображения
 */
class ImageData {

    /**
     * @param FaceData[] $facesData
     * @param bool $hasTattoo
     * @param bool $isCat
     * @param bool $isDog
     */
    public function __construct(
        public readonly array $facesData,
        public readonly bool $hasTattoo,
        public readonly bool $isCat,
        public readonly bool $isDog,
    ) {

        if($this->facesData){
            foreach ($this->facesData as $faceData){
                if(!$faceData instanceof FaceData){
                    throw new \InvalidArgumentException('Данные лиц должны быть объектами класса: '. FaceData::class);
                }
            }
        }

    }

}