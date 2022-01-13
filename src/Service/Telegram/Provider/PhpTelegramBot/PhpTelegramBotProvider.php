<?php

namespace App\Service\Telegram\Provider\PhpTelegramBot;

use App\Service\Radar\Analyzer\YakudzaRadarService;
use App\Service\Telegram\Interface\TelegramProviderInterface;
use App\Service\Telegram\TelegramHelper;
use Doctrine\ORM\EntityManagerInterface;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request as TgRequest;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Провайдер для библиотеки работы с АПИ ТГ
 *
 * @see https://github.com/php-telegram-bot/core
 */
class PhpTelegramBotProvider implements TelegramProviderInterface{

    private Telegram $client;
    private Request $request;

    public function __construct(
        string $telegramApiKey,
        string $telegramBotName,
        private LoggerInterface $logger,
        private YakudzaRadarService $yakudzaRadarService,
        private TelegramHelper $telegramHelper,
        private RequestStack $requestStack,
        private ParameterBagInterface $params,
        private MessageBusInterface $messageBus,
        private EntityManagerInterface $entityManager
    ) {

        try {
            $this->client =  new Telegram($telegramApiKey, $telegramBotName);
        } catch (TelegramException $e) {
            $this->logger->error('Не удалось инициализировать подключение к API: ' . $e->getMessage());
            throw $e;
        }

        $this->request = $this->requestStack->getCurrentRequest() ?? new Request();

        $this->client->addCommandsPath(__DIR__ . '/Commands');
        $this->configureCommands();
    }

    /**
     * Передача зависимостей в команды
     *
     * @return void
     */
    private function configureCommands() :void {

        $this->client->setCommandConfig('help', [
            'logger' => $this->logger
        ]);

        $this->client->setCommandConfig('start', [
            'logger' => $this->logger,
            'yakudzaRadarService' => $this->yakudzaRadarService,
            'telegramHelper' => $this->telegramHelper,
            'params' => $this->params,
            'messageBus' => $this->messageBus,
            'entityManager' => $this->entityManager
        ]);

        $this->client->setCommandConfig('genericmessage', [
            'logger' => $this->logger,
            'yakudzaRadarService' => $this->yakudzaRadarService,
            'telegramHelper' => $this->telegramHelper,
            'params' => $this->params,
            'messageBus' => $this->messageBus,
            'entityManager' => $this->entityManager
        ]);

    }


    /**
     * Обработка запроса
     *
     * @return void
     * @throws TelegramException
     */
    public function handleRequest() :void {

        try {

            $this->client->handle();

        } catch (TelegramException $e) {

            $this->logger->error('Не удалось обработать запрос: ' . $e->getMessage(), [
                'request' => $this->request->toArray()
            ]);
            throw $e;

        }

    }

    /**
     * Установка вебхука
     *
     * @param string $hookUrl
     * @return void
     * @throws TelegramException
     */
    public function setWebhook(string $hookUrl) :void {

        try {
            $this->client->setWebhook($hookUrl);
        } catch (TelegramException $e){
            $this->logger->error('Не удалось установить веб хук: ' . $e->getMessage());
            throw $e;
        }

    }

    /**
     * Удаление вебхука
     *
     * @return void
     * @throws TelegramException
     */
    public function removeWebhook() :void {

        try {
            $this->client->deleteWebhook();
        } catch (TelegramException $e){
            $this->logger->error('Не удалось удалить веб хук: ' . $e->getMessage());
            throw $e;
        }

    }


    /**
     * Отправка сообщения в чат
     *
     * @param int $chatID
     * @param string $text
     * @return bool
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function sendMessage(int $chatID, string $text) :bool {

        $result = TgRequest::sendMessage([
            'chat_id' => $chatID,
            'text' => $text,
        ]);

        return $result->isOk();
    }

}
