<?php

namespace App\Service\Radar\Joke;

use App\Service\ImageAnalyze\Model\ImageData;
use App\Service\Radar\Interface\JokeGenerator;

/**
 * Генератор шуток для ответа
 */
class YakudzaJokeGenerator implements JokeGenerator {

    /**
     * Выбор шутки для ответа
     *
     * @param ImageData $imageData
     * @param float $probability
     * @return string
     */
    public function getJoke(ImageData $imageData, float $probability) :string {

        $joke = 'Я не нашел никого на фото, ВНИМАНИЕ, это может значить только одно, там невероятно умело замаскировались НИНДЗЯ, они гораздо опаснее Якудза. Раскидайте ваш лук вокруг себя, НИНДЗЯ это отвлечет, они начнут его резать, и вам, невероятно рыдая, возможно удастся скрыться, надеюсь еще увидимся, хотя вряд ли конечно.';

        $jokes = [
            'human' => [
                '0-10' => [
                    'Это точно не тупой Якудза, расслабьте булки',
                    'Да вы посмотрите, это же почти ребенок, о какой такой Якудза вообще может идти речь',
                    'Смысла даже отвечать не вижу, полное отсутствие темного Якудзо-потенциала'
                ],
                '10-20' => [
                    'Сомневаюсь что это мерзкий Якудза, можете спокойно пить с этим человеком Саке до потери пульса',
                    'Этот персонаж не навредит даже цикаде, в ничтожную Якудзу таких не берут',
                    'Светлую сторону чувствую я только в этом падаване, не темно Якудзовую'
                ],
                '20-30' => [
                    'Норм чел, но возможно мечтает стать ублюдочным Якудза, безобиден',
                    'Да нееее, опасная Якудза? в худшем случае Купчарсий гопарь',
                    'Поезд под названием Якудза тудей, не остановится на станции этого человека'
                ],
                '30-40' => [
                    'Маловероятно, что это грязный Якудза, либо очень слабенький, давите его морально и он сам убежит',
                ],
                '40-50' => [
                    'Тут как с котом Шредингера - все сложно, может бесячий Якудза, а может и нет, мне то пофиг, вам же это важно'
                ],
                '50-60' => [
                    'Возможно вы наконец нашли почти поганого Якудза, купите ему подарочный напиток, и надейтесь что он это зачтет'
                ],
                '60-70' => [
                    'Возможно вы наконец нашли почти поганого Якудза, купите ему подарочный напиток, и надейтесь что он это зачтет'
                ],
                '70-80' => [
                    'Якудзость достаточно высока, я уже подумываю что и вы якудза, раз общаетесь с такими людьми, удалите меня от греха!'
                ],
                '80-100' => [
                    'Я почти уверен, что это отвратительный Якудза, прячьте кожу, пока он не набил вам татух и уходите от туда'
                ],
            ],
            'cat' => [
                '0-100' => [
                    'На фото котейка, не глупите все и тем более я знаю, что котишки не могут быть Якудза, ну разве что Ниндзя, но это уже другая история, покормите его вкусняшкой.'
                ],
            ],
            'dog' => [
                '0-100' => [
                    'На фото пёсель, алло гараж, собакены главные друзья человека, даже подумать что они могут быть Якудза - грех, вы мне отвратительны!'
                ],
            ],
        ];

        if($imageData->facesData){
            $localJokes = $jokes['human'];
        } elseif ($imageData->isCat){
            $localJokes = $jokes['cat'];
        } elseif ($imageData->isDog){
            $localJokes = $jokes['dog'];
        } else {
            $localJokes = [];
        }

        /**
         * Выбор шутки в зависимости от вероятности
         */
        foreach($localJokes as $jokeProbability => $activeJokes){

            $jokeProbabilityValues = explode('-', $jokeProbability);

            if($probability > $jokeProbabilityValues[0] && $probability <= $jokeProbabilityValues[1]){
                $joke = $activeJokes[rand(0, count($activeJokes) - 1)];
                break;
            }

        }

        return $joke;

    }
}