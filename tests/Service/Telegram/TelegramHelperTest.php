<?php

namespace App\Tests\Service\Telegram;

use App\Service\Radar\Model\AnalyzeResult;
use App\Service\Telegram\TelegramHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TelegramHelperTest extends KernelTestCase {

    private TelegramHelper $helper;
    private ParameterBagInterface $params;

    protected function setUp(): void {

        parent::setUp();

        $kernel = self::bootKernel();
        $container = static::getContainer();

        $this->helper = $container->get(TelegramHelper::class);
        $this->params = new ParameterBag([
            'telegram.api.endpoint' => 'https://api.telegram.org'
        ]);
    }

    public function testgetFileUrl(){
        $path = 'https://api.telegram.org/file/bot683133879:AAGkQxUUumPUhBdmXF-HXgS_Lp61SgEGN5A/file';
        $this->assertEquals($path, $this->helper->getFileUrl('file'));
    }

    public function testAnalyzeResultToMessage(){

        $analizeResult = new AnalyzeResult(
            'Яудза',
            '100'
        );
        $message = 'Яудза';

        $this->assertEquals($message, $this->helper->analyzeResultToMessage($analizeResult));

    }
}