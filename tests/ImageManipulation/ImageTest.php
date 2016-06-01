<?php

/**
 * Default mock settings. See: http://marcelog.github.io/articles/php_mock_global_functions_for_unit_tests_with_phpunit.html
 */
namespace {
    /** @noinspection PhpUnusedLocalVariableInspection */
    $fileExists = null;
    /** @noinspection PhpUnusedLocalVariableInspection */
    $isFile = null;
    /** @noinspection PhpUnusedLocalVariableInspection */
    $mimeContentType = null;
    /** @noinspection PhpUnusedLocalVariableInspection */
    $mockGetImageSize = false;
    /** @noinspection PhpUnusedLocalVariableInspection */
    $mockGetImageSizeWidth = 1;
    /** @noinspection PhpUnusedLocalVariableInspection */
    $mockGetImageSizeHeight = 1;
}

namespace Thinkblue\ImageManipulation {

    include 'Mocks.php';

    /**
     * Test Image class
     *
     * Class ImageTest
     * @package Tests\ImageManipulation
     */
    class ImageTest extends \PHPUnit_Framework_TestCase
    {
        public function setUp()
        {
            parent::setUp();
        }

        // ---------------------------------------------------------------- Create Image object and check error handling

        public function testNullImageGivenInConstructor()
        {
            $exceptionThrown = false;
            try {
                new Image(null);
            } catch (\InvalidArgumentException $e) {
                $exceptionThrown = true;
            }

            $this->assertTrue($exceptionThrown);
        }

        public function testInvalidFileGivenInConstructor()
        {
            $exceptionThrown = false;
            try {
                new Image('./.gitignore');
            } catch (\InvalidArgumentException $e) {
                $exceptionThrown = true;
            }

            $this->assertTrue($exceptionThrown);
        }

        public function testNotExistingFileGivenInConstructor()
        {
            $exceptionThrown = false;
            try {
                new Image('./file-not-exists');
            } catch (\InvalidArgumentException $e) {
                $exceptionThrown = true;
            }

            $this->assertTrue($exceptionThrown);
        }

        public function testInvalidMimeTypeOfFile()
        {
            $this->mockImageCreatingFunctions('application/json');

            $exceptionThrown = false;
            try {
                new Image('fake image path');
            } catch (\InvalidArgumentException $e) {
                $exceptionThrown = true;
            }

            $this->assertTrue($exceptionThrown);
        }

        // -------------------------------------------------------------------------------- Image META information tests

        public function testImageMimeTypes()
        {
            $this->mockImageCreatingFunctions(null);
            global $mimeContentType;

            $mimeTypes = ['image/gif', 'image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png'];

            foreach ($mimeTypes as $mimeType) {
                $mimeContentType = $mimeType;
                $image = new Image('fake path');
                $this->assertEquals($mimeType, $image->getMimeType());
            }
        }

        public function testImageSizeInfo()
        {
            $expectedWidth = 500;
            $expectedHeight = 300;
            $expectedAspectRatio = $expectedWidth/$expectedHeight;

            $this->mockImageCreatingFunctions('image/png');
            $this->mockImageSize($expectedWidth, $expectedHeight);

            $image = new Image('fake image path');

            $this->assertEquals($expectedWidth, $image->getWidth());
            $this->assertEquals($expectedHeight, $image->getHeight());
            $this->assertEquals($expectedAspectRatio, $image->getAspectRatio());
        }

        // ------------------------------------------------------------------------------------------------ Test helpers

        private function mockImageCreatingFunctions($forcedMimeType = 'image/png')
        {
            global $mimeContentType;
            global $fileExists;
            global $isFile;

            $fileExists = true;
            $isFile = true;
            $mimeContentType = $forcedMimeType;
        }

        private function mockImageSize($width, $height)
        {
            global $mockGetImageSize;
            global $mockGetImageSizeWidth;
            global $mockGetImageSizeHeight;

            $mockGetImageSize = true;
            $mockGetImageSizeWidth = $width;
            $mockGetImageSizeHeight = $height;
        }
    }
}