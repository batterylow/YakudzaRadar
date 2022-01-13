<?php

namespace App\Tests\Service\Telegram;

use App\Service\Telegram\Provider\PhpTelegramBot\PhpTelegramBotProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PhpTelegramBotProviderTest extends KernelTestCase {

    private PhpTelegramBotProvider $provider;

    protected function setUp(): void {

        parent::setUp();

        $kernel = self::bootKernel();
        $container = static::getContainer();

        $this->provider = $container->get(PhpTelegramBotProvider::class);
    }

    public function testInit(){
        $this->assertInstanceOf(PhpTelegramBotProvider::class, $this->provider);
    }
    
    public function testSendMessage(){

        $chatID = 249653437;
        $message = 'Test OK';

        $this->assertTrue($this->provider->sendMessage($chatID, $message));

    }

}