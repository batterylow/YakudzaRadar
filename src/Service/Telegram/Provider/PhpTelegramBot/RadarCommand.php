<?php

namespace App\Service\Telegram\Provider\PhpTelegramBot;

use App\Constants;
use App\Message\YakudzaRadarMessage;
use App\Service\ImageAnalyze\Exception\ImageAnalyzeResponseException;
use App\Service\Radar\Analyzer\YakudzaRadarService;
use App\Service\Telegram\TelegramHelper;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\PhotoSize;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Psr\Log\LoggerInterface;
use Longman\TelegramBot\Entities\Message;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class RadarCommand extends UserCommand {

    protected LoggerInterface $logger;
    protected YakudzaRadarService $yakudzaRadarService;
    protected TelegramHelper $telegramHelper;
    protected ParameterBagInterface $params;
    protected MessageBusInterface $messageBus;

    /**
     * @param Telegram $telegram
     * @param Update|null $update
     */
    public function __construct(Telegram $telegram, ?Update $update = null){

        parent::__construct($telegram, $update);

        $this->logger = $this->getConfig('logger');
        $this->yakudzaRadarService =  $this->getConfig('yakudzaRadarService');
        $this->telegramHelper =  $this->getConfig('telegramHelper');
        $this->params =  $this->getConfig('params');
        $this->messageBus =  $this->getConfig('messageBus');

        $this->validateConfig();
    }

    /**
     * Валидация конфига
     *
     * @return void
     * @throws TelegramException
     */
    private function validateConfig() :void {

        if(is_null($this->logger)){
            throw new TelegramException('Не задан логгер');
        }

        if(is_null($this->yakudzaRadarService)){
            $this->logger->error('Не задан сервис Якудзости');
            throw new TelegramException('Не задан сервис Якудзости');
        }

        if(is_null($this->telegramHelper)){
            $this->logger->error('Не задан хелпер для Телеграма');
            throw new TelegramException('Не задан хелпер для Телеграма');
        }

        if(is_null($this->params)){
            $this->logger->error('Не задан сервис настроек');
            throw new TelegramException('Не задан сервис настроек');
        }

        if(is_null($this->messageBus)){
            $this->logger->error('Не задан транспорт для сообщений');
            throw new TelegramException('Не задан транспорт для сообщений');
        }

    }

    /**
     * Запуск анлиза изображения
     *
     * @param Message $message
     * @return string
     */
    protected function analyze(Message $message) :string {

        if($message->getNewChatPhoto()){
            return '';
        }

        if($message->getPhoto()){

            /** @var PhotoSize $photo */
            $photo = $message->getPhoto()[count($message->getPhoto()) - 1];

            //Отправка задания в очередь
            if($this->params->get('app.mode') == Constants::APP_MODE_MESSAGE){

                try {

                    $this->messageBus->dispatch(new YakudzaRadarMessage(
                        $photo->getFileId(),
                        $message->getChat()->getId()
                    ));

                    $this->logger->info('Задание добавлено в очередь', [
                        'fileID' => $photo->getFileId(),
                        'chatID' => $message->getChat()->getId()
                    ]);

                    return '';

                } catch (\Throwable $e){
                    $this->logger->error('Не удалось отправить задание на анализ изображения в очередь', [
                        'message' => json_decode($message->toJson(), true)
                    ]);
                }

            }

            /**
             * Если стоит режим работы с мгновенным ответом
             * или не удалось поставить задание в очередь - отправляем сразу на анализ
             *
             */
            $file = Request::getFile([
                'file_id' => $photo->getFileId()
            ]);
            $filePath = $file->getRawData()['result']['file_path'];

            try {
                $analyzeResult = $this->yakudzaRadarService->analyze($this->telegramHelper->getFileUrl($filePath));
                $response = $this->telegramHelper->analyzeResultToMessage($analyzeResult);
            } catch (ImageAnalyzeResponseException){
                $response = ImageAnalyzeResponseException::DEFAULT_MESSAGE;
            }

        } else {
            $response = 'Мне нужно фото предполагаемого Якудза =(';
        }

        return $response;
    }
}