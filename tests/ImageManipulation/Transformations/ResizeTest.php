<?php

namespace Thinkblue\ImageManipulation\Transformations;

use Thinkblue\ImageManipulation\Image;
use Thinkblue\ImageManipulation\Utils\TestHelper;

class ResizeTest extends \PHPUnit_Framework_TestCase
{
    use TestHelper;

    public function testWidthSetting()
    {
        $maxWidth = 200;

        $resizeTransformation = new Resize();
        $resizeTransformation->setMaxWidth($maxWidth);

        $this->assertEquals($maxWidth, $resizeTransformation->getMaxWidth());
    }

    public function testHeightSettings()
    {
        $maxHeight = 200;

        $resizeTransformation = new Resize();
        $resizeTransformation->setMaxHeight($maxHeight);

        $this->assertEquals($maxHeight, $resizeTransformation->getMaxHeight());
    }

    public function testInvalidWidthSettings()
    {
        $maxWidth = 'invalid';
        $resize = new Resize();

        $exceptionThrown = false;
        try {
            $resize->setMaxWidth($maxWidth);
        } catch (\InvalidArgumentException $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    public function testInvalidHeightSettings()
    {
        $maxHeight = 'invalid';
        $resize = new Resize();

        $exceptionThrown = false;
        try {
            $resize->setMaxHeight($maxHeight);
        } catch (\InvalidArgumentException $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    public function testAspectRatioSettingsTrue()
    {
        $preserveAspectRatioValues = [false, true];

        foreach ($preserveAspectRatioValues as $value) {
            $resizeTransformation = new Resize();
            $resizeTransformation->setPreserveAspectRatio($value);

            $this->assertEquals($value, $resizeTransformation->isPreserveAspectRatio());
        }
    }
    
    public function testAddTransformationToImage()
    {
        $imageName = 'test.png';
        $imagePath = $this->createImage($imageName);

        $resize = new Resize();
        
        $image = new Image($imagePath);
        $image->addTransformation($resize);
        
        $this->assertFalse(empty($image->getTransformations()));
    }

    public function testSetPreserveAspectRatio()
    {
        $aspectRatioValues = [true, false];

        foreach ($aspectRatioValues as $value) {
            $resize = new Resize();
            $resize->setPreserveAspectRatio($value);
            $this->assertEquals($value, $resize->isPreserveAspectRatio());
        }
    }

    public function testSetPreserveAspectRatioInvalidValue()
    {
        $aspectRatioValue = 'invalid';

        $resize = new Resize();
        $exceptionThrown = false;
        try {
            $resize->setPreserveAspectRatio($aspectRatioValue);
        } catch (\InvalidArgumentException $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }
}