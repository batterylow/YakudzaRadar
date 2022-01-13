<?php
namespace App\Command\Telegram;

use App\Service\Telegram\TelegramService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда для установки вебкуха для получения запросов от ТГ
 */
class SetWebHookCommand extends Command
{

    /**
     * The name of the command (the part after "bin/console")
     *
     * @var string
     */
    protected static $defaultName = 'app:telegram:hook:set';


    public function __construct(
        private TelegramService $telegramService
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Команда передаёт телеграму URL вебхука для уведомлений')
            ->setHelp('URL определяется в App\Service\Telegram\TelegramService')
        ;
    }

    /**
     * Выполнение команды.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output){

        try {

            $hookUrl = $this->telegramService->setWebhook();
            $output->writeln(' <info>SUCCESS</info>');
            $output->writeln(' <info>Set hook URL: ' . $hookUrl . '</info>');

        } catch (\Throwable $e){
            $output->writeln(' <error>'. $e->getMessage() .'</error>');

        }

        return 0;
    }

}