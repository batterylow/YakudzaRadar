<?php

namespace App\Tests\Service\Radar;

use App\Service\ImageAnalyze\Model\FaceData;
use App\Service\ImageAnalyze\Model\ImageData;
use App\Service\Radar\Analyzer\YakudzaRadarService;
use App\Service\Radar\Enum\Gender;
use App\Service\Radar\Model\AnalyzeResult;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class YakudzaRadarServiceTest extends KernelTestCase {

    private array $images;
    private YakudzaRadarService $yakudzaRadarService;

    protected function setUp(): void {

        parent::setUp();

        $kernel = self::bootKernel();
        $container = static::getContainer();

        $this->yakudzaRadarService = $container->get(YakudzaRadarService::class);
        $this->images = [
            $kernel->getProjectDir() . '/public/storage/files/boy.jpg'
        ];

    }


    public function testAnalyze(){

        foreach($this->images as $image){

            $analizeResults = $this->yakudzaRadarService->analyze($image);

            $this->assertInstanceOf(AnalyzeResult::class, $analizeResults);
            $this->assertIsString($analizeResults->message);
            $this->assertGreaterThan(0, $analizeResults->probability);
        }

    }

    public function testCheckYakudza()
    {

        $faceData = new FaceData(15, Gender::FEMALE, false);
        $hasTattoo = true;

        $checkYakudza = self::getMethod('checkYakudza', YakudzaRadarService::class);
        $yakudzaProbability = $checkYakudza->invokeArgs($this->yakudzaRadarService, [
            $faceData,
            $hasTattoo
        ]);

        $this->assertEqualsWithDelta(34.76, $yakudzaProbability, 4);
    }

    public function testCheckYakudzas(){

        $facesData = [
            new FaceData(15, Gender::FEMALE, false),
            new FaceData(15, Gender::FEMALE, false)
        ];
        $hasTattoo = true;

        $checkYakudzas = self::getMethod('checkYakudzas', YakudzaRadarService::class);
        $yakudzasProbability = $checkYakudzas->invokeArgs($this->yakudzaRadarService, [
            $facesData,
            $hasTattoo
        ]);

        $this->assertIsArray($yakudzasProbability);
        foreach($yakudzasProbability as $yakudzaProbability){
            $this->assertEqualsWithDelta(34.76, $yakudzaProbability, 4);
        }

    }



    protected static function getMethod($name, $className) {
        $class = new \ReflectionClass($className);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

}