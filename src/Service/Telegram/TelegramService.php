<?php

namespace App\Service\Telegram;

use App\Service\Telegram\Interface\TelegramProviderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Сервис для работы с ТГ
 */
class TelegramService {

    public function __construct(
        public TelegramProviderInterface $provider,
        private UrlGeneratorInterface $urlGenerator,
        private string $telegramApiKey
    ) {}

    public function setWebhook() :string {

        $webHookUrl = $this->urlGenerator->generate('hook', ['token' => $this->telegramApiKey], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->provider->setWebhook($webHookUrl);

        return $webHookUrl;
    }

}