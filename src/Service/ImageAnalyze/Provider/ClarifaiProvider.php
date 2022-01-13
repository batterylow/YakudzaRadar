<?php

namespace App\Service\ImageAnalyze\Provider;

use App\Service\ImageAnalyze\Exception\ImageAnalyzeResponseException;
use App\Service\ImageAnalyze\Interface\ImageAnalyzeProviderInterface;
use App\Service\ImageAnalyze\Model\FaceData;
use App\Service\ImageAnalyze\Model\ImageData;
use App\Service\Radar\Enum\Gender;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;
use Clarifai\Api\PostWorkflowResultsRequest;
use Clarifai\Api\V2Client;
use Clarifai\ClarifaiClient;
use Clarifai\Api\Data;
use Clarifai\Api\Image;
use Clarifai\Api\Input;
use Clarifai\Api\Status\StatusCode;
use Clarifai\Api\PostWorkflowResultsResponse;
use Clarifai\Api\Output;

/**
 * Провайдер для работы с неросетью по анализу изображений Clarifai
 *
 * @see https://docs.clarifai.com/
 */
class ClarifaiProvider implements ImageAnalyzeProviderInterface {

    private readonly V2Client $client;
    private readonly array $metadata;

    public function __construct(
        string $clarifaiApiKey,
        private readonly string $clarifaiAppID,
        private readonly LoggerInterface $logger,
        private ParameterBagInterface $parameterBag,
    ) {

        $this->client = ClarifaiClient::grpc();
        $this->metadata = ['Authorization' => ['Key ' . $clarifaiApiKey]];
    }

    /**
     * Получение данных изображения
     *
     * @param string $image Путь или URL к изображению для анализа
     * @return ImageData
     */
    public function getImageData(string $image) :ImageData {

        //Значения данных по-умолчанию
        $facesData = [];
        $hasTattoo = false;
        $isCat = false;
        $isDog = false;

        /** @var PostWorkflowResultsResponse $response */
        [$response, $status] = $this->client->PostWorkflowResults(
            new PostWorkflowResultsRequest([
                'workflow_id' => $this->parameterBag->get('clarifai.workflow.analyze.id'),
                'inputs' => [
                    new Input([
                        'data' => new Data([
                            'image' => $this->buildImageObject($image)
                        ])
                    ])
                ],
                'favor_clarifai_workflows' => true
            ]),
            $this->metadata
        )->wait();

        if ($status->code !== 0) {
            $this->logger->error('Неудачный запрос к АПИ нейросети: ' . $status->details);
            throw new ImageAnalyzeResponseException($status->details);
        }

        if ($response->getStatus()->getCode() != StatusCode::SUCCESS) {
            $this->logger->error('Ошибка запроса к АПИ нейросети: ' . $response->getStatus()->getDescription() .' '. $response->getStatus()->getDetails());
            throw new ImageAnalyzeResponseException($response->getStatus()->getDescription() .' '. $response->getStatus()->getDetails());
        }

        /** @var Output[] $outputs */
        $outputs = $response->getResults() ? $response->getResults()[0]->getOutputs() : false;

        if($outputs){

            $faces = [];
            foreach ($outputs as $output) {

                //Определяем рассу
                if($output->getModel()->getId() == $this->parameterBag->get('clarifai.model.multicultural.id')){
                    foreach($output->getData()->getRegions() as $rKey => $region){
                        $faces[$rKey]['isAsian'] = str_contains(strtolower($region->getData()->getConcepts()[0]->getName()), 'asian');
                    }
                }

                //Определяем пол
                if($output->getModel()->getId() == $this->parameterBag->get('clarifai.model.gender.id')){
                    foreach($output->getData()->getRegions() as $rKey => $region){
                        $faces[$rKey]['gender'] = match ($region->getData()->getConcepts()[0]->getName()) {
                            'Masculine' => Gender::MALE,
                            'Feminine' => Gender::FEMALE,
                        };
                    }
                }

                //Определяем примерный возраст
                if($output->getModel()->getId() == $this->parameterBag->get('clarifai.model.age.id')){
                    foreach($output->getData()->getRegions() as $rKey => $region){
                        $ages = explode('-', $region->getData()->getConcepts()[0]->getName());
                        $faces[$rKey]['age'] = count($ages) > 1 ? rand($ages[0], $ages[1]) : $ages[0];
                    }
                }

                //Считываем дополнтельную информацию с фото
                if($output->getModel()->getId() == $this->parameterBag->get('clarifai.model.general.id')){
                    foreach($output->getData()->getConcepts() as $concept){

                        //Есть ли татушки
                        if($concept->getName() == 'tattoo' && $concept->getValue() >= $this->parameterBag->get('clarifai.concept.value.limit')){
                            $hasTattoo = true;
                        }

                        //Котики
                        if($concept->getName() == 'cat' && $concept->getValue() >= $this->parameterBag->get('clarifai.concept.value.limit')){
                            $isCat = true;
                        }

                        //Собакены
                        if($concept->getName() == 'dog' && $concept->getValue() >= $this->parameterBag->get('clarifai.concept.value.limit')){
                            $isDog = true;
                        }

                    }
                }

            }

            //Формируем модель лиц
            foreach($faces as $face){
                $facesData[] = new FaceData($face['age'], $face['gender'], $face['isAsian']);
            }

        } else {
            $this->logger->error('Нет результатов анализа', [
                'image' => $image
            ]);
        }

        return new ImageData($facesData, $hasTattoo, $isCat, $isDog);

    }


    /**
     * Формирование данных изображения для запроса
     *
     * @param string $image
     * @return Image
     */
    private function buildImageObject(string $image) :Image {

        if(is_file($image)){

            $imageObject = new Image([
                'base64' => file_get_contents($image)
            ]);

        } else {

            $imageObject = new Image([
                'url' => $image
            ]);

        }

        return $imageObject;
    }

}