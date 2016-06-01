<?php

namespace Thinkblue\ImageManipulation\Transformations;

class ResizeTest extends \PHPUnit_Framework_TestCase
{
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

    public function testAspectRatioSettingsTrue()
    {
        $preserveAspectRatioValues = [false, true];

        foreach ($preserveAspectRatioValues as $value) {
            $resizeTransformation = new Resize();
            $resizeTransformation->setPreserveAspectRatio($value);

            $this->assertEquals($value, $resizeTransformation->isPreserveAspectRatio());
        }
    }
}