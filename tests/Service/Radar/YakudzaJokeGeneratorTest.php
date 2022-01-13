<?php

namespace App\Tests\Service\Radar;

use App\Service\ImageAnalyze\Model\FaceData;
use App\Service\ImageAnalyze\Model\ImageData;
use App\Service\Radar\Enum\Gender;
use App\Service\Radar\Joke\YakudzaJokeGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class YakudzaJokeGeneratorTest extends KernelTestCase {

    private YakudzaJokeGenerator $jokeGenerator;

    protected function setUp(): void {

        parent::setUp();

        $kernel = self::bootKernel();
        $container = static::getContainer();

        $this->jokeGenerator = $container->get(YakudzaJokeGenerator::class);

    }


    public function testGetJoke(){

        $imageData = new ImageData(
            [new FaceData(15, Gender::MALE, false)],
            false,
            false,
            false
        );
        $probability = 60;

        $joke = $this->jokeGenerator->getJoke($imageData, $probability);

        $this->assertIsString($joke);

    }

}