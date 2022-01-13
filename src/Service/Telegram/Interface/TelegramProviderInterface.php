<?php

namespace App\Service\Telegram\Interface;

use Longman\TelegramBot\Exception\TelegramException;

/**
 * Интерфейс для провайдера работы с АПИ ТГ
 */
interface TelegramProviderInterface {

    /**
     * Обработка запроса
     *
     * @return void
     */
    public function handleRequest() :void;

    /**
     * Установка вебхука
     *
     * @param string $hookUrl
     * @return void
     * @throws TelegramException
     */
    public function setWebhook(string $hookUrl) :void;

    /**
     * Удаление вебхука
     *
     * @return void
     * @throws TelegramException
     */
    public function removeWebhook() :void;

    /**
     * Отправка сообщения в чат
     *
     * @param int $chatID
     * @param string $text
     * @return bool
     */
    public function sendMessage(int $chatID, string $text) :bool;

}