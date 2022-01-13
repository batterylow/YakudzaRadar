<?php

/**
 * User "/start" command
 *
 * Команда старта анализа изображения
 *
 */

namespace App\Service\Telegram\Provider\PhpTelegramBot\Commands;

use App\Service\Telegram\Provider\PhpTelegramBot\RadarCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;

class StartCommand extends RadarCommand {
    /**
     * @var string
     */
    protected $name = 'start';

    /**
     * @var string
     */
    protected $description = 'Запуск анализа изображения';

    /**
     * @var string
     */
    protected $usage = '/start';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var bool
     */
    protected $private_only = false;


    /**
     * @var bool
     */
    protected $show_in_help = true;


    /**
     * Выполнение команды
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse {

        $message = $this->getMessage();

        return $this->replyToChat(
            $this->analyze($message)
        );

    }
}