<?php

namespace App\Service\ImageAnalyze\Model;

use App\Service\Radar\Enum\FaceType;
use App\Service\Radar\Enum\Gender;

/**
 * Модель данных лица
 */
class FaceData {

    /**
     * Тип лица
     *
     * @var FaceType
     */
    public readonly FaceType $faceType;

    /**
     * @param int $age Возраст
     * @param Gender $gender Пол
     * @param bool $isAsian Азиатость
     */
    public function __construct(
        public readonly int $age,
        public readonly Gender $gender,
        public readonly bool $isAsian,
    ) {
        $this->faceType = $this->getFaceType();
    }

    /**
     * Получение типа лица
     *
     * @return FaceType
     */
    private function getFaceType() :FaceType {

        if($this->isAsian){

            $faceType = $this->age >= 18 ? FaceType::ASIAN_MAN : FaceType::ASIAN_BOY;

            if($this->gender == Gender::FEMALE){
                $faceType = $this->age >= 18 ? FaceType::ASIAN_WOMAN : FaceType::ASIAN_GIRL;
            }

        } else {

            $faceType = $this->age >= 18 ? FaceType::MAN : FaceType::BOY;

            if($this->gender == Gender::FEMALE){
                $faceType = $this->age >= 18 ? FaceType::WOMAN : FaceType::GIRL;
            }

        }

        return $faceType;
    }

}