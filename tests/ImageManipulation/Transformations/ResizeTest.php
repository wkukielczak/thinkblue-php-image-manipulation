<?php

namespace Thinkblue\ImageManipulation\Transformations;

use Thinkblue\ImageManipulation\Image;
use Thinkblue\ImageManipulation\Utils\TestHelper;

class ResizeTest extends \PHPUnit_Framework_TestCase
{
    use TestHelper;

    /** @var Resize */
    private $resize;

    protected function setUp()
    {
        parent::setUp();
        $this->resize = new Resize();
    }

    public function testWidthSetting()
    {
        $maxWidth = 200;
        $this->resize->setMaxWidth($maxWidth);

        $this->assertEquals($maxWidth, $this->resize->getMaxWidth());
    }

    public function testHeightSettings()
    {
        $maxHeight = 200;
        $this->resize->setMaxHeight($maxHeight);

        $this->assertEquals($maxHeight, $this->resize->getMaxHeight());
    }

    public function testInvalidWidthSettings()
    {
        $maxWidth = 'invalid';

        $exceptionThrown = false;
        try {
            $this->resize->setMaxWidth($maxWidth);
        } catch (\InvalidArgumentException $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    public function testInvalidHeightSettings()
    {
        $maxHeight = 'invalid';

        $exceptionThrown = false;
        try {
            $this->resize->setMaxHeight($maxHeight);
        } catch (\InvalidArgumentException $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    public function testAspectRatioSettingsTrue()
    {
        $preserveAspectRatioValues = [false, true];

        foreach ($preserveAspectRatioValues as $value) {
            $this->resize->setPreserveAspectRatio($value);
            $this->assertEquals($value, $this->resize->isPreserveAspectRatio());
        }
    }
    
    public function testAddTransformationToImage()
    {
        $imageName = 'test.png';
        $imagePath = $this->createImage($imageName);
        
        $image = new Image($imagePath);
        $image->addTransformation($this->resize);
        
        $this->assertFalse(empty($image->getTransformations()));
    }

    public function testSetPreserveAspectRatio()
    {
        $aspectRatioValues = [true, false];

        foreach ($aspectRatioValues as $value) {
            $this->resize->setPreserveAspectRatio($value);
            $this->assertEquals($value, $this->resize->isPreserveAspectRatio());
        }
    }

    public function testSetPreserveAspectRatioInvalidValue()
    {
        $aspectRatioValue = 'invalid';

        $exceptionThrown = false;
        try {
            $this->resize->setPreserveAspectRatio($aspectRatioValue);
        } catch (\InvalidArgumentException $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    public function testPreservePngImageTransparency()
    {
        $this->resize->setPreservePngTransparency(true);
        $this->assertTrue($this->resize->isPreservePngTransparency());
    }

    public function testGetTransformationName()
    {
        $this->assertEquals('Resize', $this->resize->getTransformationName());
    }

    // Apply transformation, portrait
    public function testTransformingPortraitImage()
    {
        $tmpDir = sys_get_temp_dir();
        $outputFile = $tmpDir . '/img_transformed.jpg';
        $expectedWidth = 200;

        $this->resize->setMaxWidth($expectedWidth);
        $this->resize->setPreserveAspectRatio(true);

        $imageFile = $this->createImage('img.jpg', 500, 300);
        $image = new Image($imageFile);
        $image->addTransformation($this->resize);
        $image->applyTransformations();
        $image->saveTransformedFile($outputFile);

        // Check if file is saved
        $this->assertTrue(is_file($outputFile));

        // Check if the image has expected width
        $outputImage = new Image($outputFile);
        $this->assertEquals($expectedWidth, $outputImage->getWidth());
    }

    // Apply transformation, landscape
    public function testTransformingLandscapeImage()
    {
        $tmpDir = sys_get_temp_dir();
        $outputFile = $tmpDir . '/img_transformed.png';
        $expectedHeight = 500;

        $this->resize->setMaxHeight($expectedHeight);
        $this->resize->setPreserveAspectRatio(true);

        $imageFile = $this->createImage('img.png', 700, 1900);
        $image = new Image($imageFile);
        $image->addTransformation($this->resize);
        $image->applyTransformations();
        $image->saveTransformedFile($outputFile);

        // Check if file is saved
        $this->assertTrue(is_file($outputFile));

        // Check if the image has expected height
        $outputImage = new Image($outputFile);
        $this->assertEquals($expectedHeight, $outputImage->getHeight());
    }

    // Apply transformation, square
    public function testTransformingSquareImage()
    {
        $tmpDir = sys_get_temp_dir();
        $outputFile = $tmpDir . '/img_transformed.gif';
        $expectedHeight = 500;
        $expectedWidth = 500;

        $this->resize->setMaxHeight($expectedHeight);

        $imageFile = $this->createImage('img.gif', 500, 500);
        $image = new Image($imageFile);
        $image->addTransformation($this->resize);
        $image->applyTransformations();
        $image->saveTransformedFile($outputFile);

        $this->assertEquals('image/gif', $image->getMimeType());

        // Check if file is saved
        $this->assertTrue(is_file($outputFile));

        // Check if the image has expected height
        $outputImage = new Image($outputFile);
        $this->assertEquals($expectedHeight, $outputImage->getHeight());
        $this->assertEquals($expectedWidth, $outputImage->getWidth());
    }

    public function testTransformationNotSetCorrectly()
    {
        $imageFile = $this->createImage('img.gif', 500, 500);
        $image = new Image($imageFile);
        $image->addTransformation($this->resize);

        $exceptionThrown = false;

        // Should throw an exception
        try {
            $image->applyTransformations();
        } catch (\InvalidArgumentException $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    public function testPreservePngTransparency()
    {
        $tmpDir = sys_get_temp_dir();
        $outputFile = $tmpDir . '/img_transformed.png';
        $expectedHeight = 500;
        $expectedWidth = 500;

        $this->resize->setMaxHeight($expectedHeight);
        $this->resize->setMaxWidth($expectedWidth);
        $this->resize->setPreserveAspectRatio(false);
        $this->resize->setPreservePngTransparency(true);

        $imageFile = $this->createImage('img.png', 500, 500);
        $image = new Image($imageFile);
        $image->addTransformation($this->resize);
        $image->applyTransformations();
        $image->saveTransformedFile($outputFile);

        // Check if file is saved
        $this->assertTrue(is_file($outputFile));

        // Check if the image has expected height
        $outputImage = new Image($outputFile);
        $this->assertEquals($expectedHeight, $outputImage->getHeight());
        $this->assertEquals($expectedWidth, $outputImage->getWidth());
    }

    public function testUseCaseNotKnown()
    {
        $this->resize->setMaxWidth(200);
        $this->resize->setMaxHeight(200);
        $this->resize->setPreserveAspectRatio(true);

        $imageFile = $this->createImage('img.png', 500, 300);
        $image = new Image($imageFile);
        $image->addTransformation($this->resize);

        $exceptionThrown = false;
        try {
            $image->applyTransformations();
        } catch (\Exception $e) {
            $exceptionThrown = true;
        }

        // Check if file is saved
        $this->assertTrue($exceptionThrown);
    }

}