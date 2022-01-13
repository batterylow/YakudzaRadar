<?php

namespace App\Service\Telegram;

use App\Service\Radar\Model\AnalyzeResult;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Хелпер для работы с ТГ
 */
class TelegramHelper {

    public function __construct(
        private readonly string $telegramApiKey,
        private ParameterBagInterface $params
    ) {}

    /**
     * Получение публичной ссылки на файл в ТГ
     *
     * @param string $filePath Путь к файлу в хранилище ТГ
     * @return string
     */
    public function getFileUrl(string $filePath) : string {
        return $this->params->get('telegram.api.endpoint') . '/file/bot' . $this->telegramApiKey . '/' . $filePath;
    }

    /**
     * Форматирование результата анализа в сообщение
     *
     * @param AnalyzeResult $analyzeResult
     * @return string
     */
    public function analyzeResultToMessage(AnalyzeResult $analyzeResult) :string {
        return sprintf('%s',
            $analyzeResult->message
        );
    }

}