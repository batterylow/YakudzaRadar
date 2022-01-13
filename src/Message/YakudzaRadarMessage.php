<?php

namespace App\Message;

/**
 * Cообщение для анализа Якудзоси фото из ТГ
 */
class YakudzaRadarMessage {

    /**
     * @param string $tgFileID ID изображения для анализа в хранилище ТГ
     * @param string $tgChatID ID чата в ТГ
     * @param int $logMessageID ID сообщения в логе
     */
    public function __construct(
        public readonly string $tgFileID,
        public readonly string $tgChatID,
        public readonly int $logMessageID
    ) {}


}