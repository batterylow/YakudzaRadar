<?php

namespace App\Service\Radar\Analyzer;

use App\Service\ImageAnalyze\ImageAnalyzeService;
use App\Service\ImageAnalyze\Model\FaceData;
use App\Service\Radar\Enum\Gender;
use App\Service\Radar\Interface\JokeGenerator;
use App\Service\Radar\Model\AnalyzeResult;

/**
 * Сервис якудзирования
 */
class YakudzaRadarService {

    /**
     * @param JokeGenerator $jokeGenerator
     * @param ImageAnalyzeService $imageAnalyzeService
     */
    public function __construct(
        private JokeGenerator $jokeGenerator,
        private ImageAnalyzeService $imageAnalyzeService
    ) {}

    /**
     * Анализ изображения на якудзовость
     *
     * @param string $image Путь или URL к изображению для анализа
     *
     * @return AnalyzeResult
     */
    public function analyze(string $image) :AnalyzeResult {

        $imageData = $this->imageAnalyzeService->provider->getImageData($image);

        if($imageData->facesData){

            $probabilities = $this->checkYakudzas($imageData->facesData, $imageData->hasTattoo);

            if(count($probabilities) > 1){

                $probability = round(array_sum($probabilities) / count($probabilities), 2);
                $message = 'На фото несколько человек, давайте рассмотрим их:' . PHP_EOL;

                foreach($probabilities as $pKey => $oneProbability){

                    $message .= sprintf('(%d) %s %s, Якудзо-вероятность: %s%%. %s' . PHP_EOL,
                        ($pKey + 1),
                        mb_convert_case($imageData->facesData[$pKey]->faceType->title(), MB_CASE_TITLE, 'UTF-8'),
                        \morphos\Russian\pluralize($imageData->facesData[$pKey]->age, 'год'),
                        $oneProbability,
                        $this->jokeGenerator->getJoke($imageData, $oneProbability)
                    );

                }

            } else {

                $probability = $probabilities[0];
                $message = sprintf('На фото %s %s, Якудзо-вероятность: %s%%, %s' . PHP_EOL,
                    $imageData->facesData[0]->faceType->title(),
                    \morphos\Russian\pluralize($imageData->facesData[0]->age, 'год'),
                    $probability,
                    $this->jokeGenerator->getJoke($imageData, $probability)
                );
            }

        } else {
            //TODO: использовать и вероятность нахождения животных
            $probability = 100;
            $message = $this->jokeGenerator->getJoke($imageData, $probability);
        }

        return new AnalyzeResult($message, $probability);

    }

    /**
     * Вычисление Якудзости для человека
     *
     * @param FaceData $faceData Данные лица
     * @param bool $hasTattoo Есть ли на фото татуировки
     * @return float
     */
    private function checkYakudza(FaceData $faceData, bool $hasTattoo) :float {

        $yakudzaProbability = 0;

        $asianWeight = 22;
        $asianCorrection = 2;
        $genderWeight = 8;
        $genderCorrection = 2;
        $ageWeight = 32;
        $ageCorrection = 4;
        $tattooWeight = 30;

        $maxYakudzaAge = 85;

        if($faceData->isAsian){
            $yakudzaProbability += ($asianWeight + rand(0, $asianCorrection ));
        }

        if($faceData->gender == Gender::MALE){
            $yakudzaProbability += ($genderWeight + rand(0, $genderCorrection));
        }

        $kAge = round(($faceData->age * $ageWeight / $maxYakudzaAge), 2);
        if($kAge > $ageWeight){
            $kAge = 0;
        }

        $yakudzaProbability += $kAge + rand (0, $ageCorrection);

        $yakudzaProbability += ($tattooWeight * round($hasTattoo, 2, PHP_ROUND_HALF_DOWN));

        $yakudzaProbability = $yakudzaProbability < 0 ? 0 : $yakudzaProbability;
        $yakudzaProbability = $yakudzaProbability > 100 ? 100 : $yakudzaProbability;

        return $yakudzaProbability;

    }

    /**
     * Вычисление Якудзости для группы лиц
     *
     * @param array $facesData Данные лиц
     * @param bool $hasTattoo Есть ли на фото татуировки
     * @return array
     */
    private function checkYakudzas(array $facesData, bool $hasTattoo) :array {

        $yakudzasProbability = array_map(function(FaceData $faceData) use ($hasTattoo){
            return $this->checkYakudza($faceData, $hasTattoo);
        }, $facesData);

        return $yakudzasProbability;
    }

}