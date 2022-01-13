<?php

/*
 * Generic message command
 *
 * Gets executed when any type of message is sent.
 *
 * In this message-related context, we can handle any kind of message.
 */

namespace App\Service\Telegram\Provider\PhpTelegramBot\Commands;

use App\Service\Telegram\Provider\PhpTelegramBot\RadarCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class GenericmessageCommand extends RadarCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';

    /**
     * @var string
     */
    protected $description = 'Handle generic message';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * @var bool
     */
    protected $show_in_help = false;

    /**
     * Main command execution
     *
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {

        $message = $this->getMessage();

        return $this->replyToChat(
            $this->analyze($message)
        );

    }

}