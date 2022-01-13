<?php

namespace App\MessageHandler;

use App\Entity\Message;
use App\Message\YakudzaRadarMessage;
use App\Service\ImageAnalyze\Exception\ImageAnalyzeResponseException;
use App\Service\Radar\Analyzer\YakudzaRadarService;
use App\Service\Telegram\TelegramHelper;
use App\Service\Telegram\TelegramService;
use Doctrine\ORM\EntityManagerInterface;
use Longman\TelegramBot\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Обработчик сообщения по анализу Якудзоси фото из ТГ
 */
class YakudzaRadarMessageHandler implements MessageHandlerInterface {

    public function __construct(
        private LoggerInterface $logger,
        private TelegramService $telegramService,
        private YakudzaRadarService $yakudzaRadarService,
        private TelegramHelper $telegramHelper,
        private EntityManagerInterface $entityManager
    ) { }

    public function __invoke(YakudzaRadarMessage $message)
    {

        try {

            $file = Request::getFile([
                'file_id' => $message->tgFileID
            ]);
            $filePath = $file->getRawData()['result']['file_path'];

            try {

                $analyzeResult = $this->yakudzaRadarService->analyze($this->telegramHelper->getFileUrl($filePath));
                $response = $this->telegramHelper->analyzeResultToMessage($analyzeResult);

            } catch (ImageAnalyzeResponseException){
                $response = ImageAnalyzeResponseException::DEFAULT_MESSAGE;
            }

            $this->telegramService->provider->sendMessage($message->tgChatID, $response);

            //Пишем результат в лог
            $logMessage = $this->entityManager->getRepository(Message::class)->find($message->logMessageID);
            $logMessage
                ->setResponse($response)
                ->setResponsed(new \DateTime())
            ;
            $this->entityManager->flush();

        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            throw $e;
        }

    }

}