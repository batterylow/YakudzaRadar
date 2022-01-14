<?php

namespace App\Service\Telegram;

use App\Service\Telegram\Interface\TelegramProviderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Сервис для работы с ТГ
 */
class TelegramService {

    public function __construct(
        public TelegramProviderInterface $provider,
        private TelegramHelper $telegramHelper,
        private UrlGeneratorInterface $urlGenerator,
        private string $telegramApiKey,
        private Filesystem $filesystem
    ) {}

    /**
     * Установка вебхука
     *
     * @return string
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function setWebhook() :string {

        $webHookUrl = $this->urlGenerator->generate('hook', ['token' => $this->telegramApiKey], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->provider->setWebhook($webHookUrl);

        return $webHookUrl;
    }

    /**
     * Получение ссылки на файл в хранилище ТГ
     * Действует 1 час
     *
     * @param string $fileID
     * @return string
     */
    public function getFileUrl(string $fileID) :string {
        $filePath = $this->provider->getFilePath($fileID);
        return $this->telegramHelper->getFileUrl($filePath);
    }

    /**
     * Загрузка файла для анализа
     *
     * @param string $fileID
     * @param string|null $filePath
     * @return string
     */
    public function downloadFile(string $fileID, ?string $filePath = null) :string {

        $filePath = $filePath ?? $this->filesystem->tempnam('/tmp', 'tg_');
        $fileUrl = $this->getFileUrl($fileID);

        $this->filesystem->dumpFile($filePath, file_get_contents($fileUrl));

        return $filePath;
    }

}