<?php

namespace App\Tests\Service\ImageAnalyze;

use App\Service\ImageAnalyze\Model\FaceData;
use App\Service\ImageAnalyze\Model\ImageData;
use App\Service\ImageAnalyze\Provider\ClarifaiProvider;
use App\Service\Radar\Enum\Gender;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Clarifai\Api\Image;

class ClarifaiProviderTest extends KernelTestCase {

    private array $images;
    private ClarifaiProvider $provider;

    protected function setUp(): void {

        parent::setUp();

        $kernel = self::bootKernel();
        $container = static::getContainer();

        $this->provider = $container->get(ClarifaiProvider::class);

        $this->images = [
            $kernel->getProjectDir() . '/public/storage/files/boy.jpg' => new ImageData(
                [new FaceData(15, Gender::MALE, false)],
                false,
                false,
                false
            )
        ];
    }


    public function testInit(){
        $this->assertInstanceOf(ClarifaiProvider::class, $this->provider);
    }

    public function testBuildImageObject()
    {

        $buildImageObject = self::getMethod('buildImageObject', ClarifaiProvider::class);
        $imageObject = $buildImageObject->invokeArgs($this->provider, [
            array_key_first($this->images)
        ]);

       $this->assertInstanceOf(Image::class, $imageObject);
    }

    public function testGetImageData(){

        foreach($this->images as $imagePath => $image){

            $imageData = $this->provider->getImageData($imagePath);

            $this->assertInstanceOf(ImageData::class, $imageData);

            $this->assertEquals(1, count($imageData->facesData));

            foreach($imageData->facesData as $fdKey => $facesData){
                $this->assertEquals($facesData->gender, $image->facesData[$fdKey]->gender);
                $this->assertEquals($facesData->isAsian, $image->facesData[$fdKey]->isAsian);
            }

            $this->assertEquals($image->hasTattoo, $imageData->hasTattoo);
            $this->assertEquals($image->isCat, $imageData->isCat);
            $this->assertEquals($image->isDog, $imageData->isDog);

        }

    }

    protected static function getMethod($name, $className) {
        $class = new \ReflectionClass($className);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }


}