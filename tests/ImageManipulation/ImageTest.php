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

    use Thinkblue\ImageManipulation\Utils\TestHelper;

    include 'Utils/Mocks.php';

    /**
     * Test Image class
     *
     * Class ImageTest
     * @package Tests\ImageManipulation
     */
    class ImageTest extends \PHPUnit_Framework_TestCase
    {
        use TestHelper;

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

        // ----------------------------------------------------------------------------------------------- Get file info

        public function testFileInfo()
        {
            $imageFileName = 'image';
            $imageFileExtension = 'jpg';
            $imageName = $imageFileName . '.' . $imageFileExtension;

            $imagePath = $this->createImage($imageName);
            $pathInfo = pathinfo($imagePath);

            // Test three times to cover all the lines hidden by values lazy loading
            $image1 = new Image($imagePath);
            $this->assertEquals($imageName, $image1->getFileName());
            $this->assertEquals($imageFileExtension, $image1->getFileExtension());
            $this->assertEquals($pathInfo['dirname'], $image1->getFilePath());

            $image2 = new Image($imagePath);
            $this->assertEquals($imageFileExtension, $image2->getFileExtension());
            $this->assertEquals($imageName, $image2->getFileName());
            $this->assertEquals($pathInfo['dirname'], $image2->getFilePath());

            $image3 = new Image($imagePath);
            $this->assertEquals($pathInfo['dirname'], $image3->getFilePath());
            $this->assertEquals($imageFileExtension, $image3->getFileExtension());
            $this->assertEquals($imageName, $image3->getFileName());
        }

        // --------------------------------------------------------- Test if the image resource was created successfully

        public function testJpgImageResource()
        {
            $imagePath = $this->createImage('img.jpg');
            $image = new Image($imagePath);
            $this->assertTrue(is_resource($image->getImageResource()));
        }

        public function testPngImageResource()
        {
            $imagePath = $this->createImage('img.png');
            $image = new Image($imagePath);
            $this->assertTrue(is_resource($image->getImageResource()));
        }

        public function testGifImageResource()
        {
            $imagePath = $this->createImage('img.gif');
            $image = new Image($imagePath);
            $this->assertTrue(is_resource($image->getImageResource()));
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

        public function testImageWidthInfo()
        {
            $expectedWidth = 500;
            $expectedHeight = 300;

            $this->mockImageCreatingFunctions('image/png');
            $this->mockImageSize($expectedWidth, $expectedHeight);

            $image = new Image('fake image path');

            $this->assertEquals($expectedWidth, $image->getWidth());
        }

        public function testImageHeightInfo()
        {
            $expectedWidth = 500;
            $expectedHeight = 300;

            $this->mockImageCreatingFunctions('image/png');
            $this->mockImageSize($expectedWidth, $expectedHeight);

            $image = new Image('fake image path');

            $this->assertEquals($expectedHeight, $image->getHeight());
        }

        public function testAspectRatioInfoLandscape()
        {
            $expectedWidth = 500;
            $expectedHeight = 300;
            $expectedAspectRatio = $expectedWidth/$expectedHeight;

            $this->mockImageCreatingFunctions('image/jpeg');
            $this->mockImageSize($expectedWidth, $expectedHeight);

            $image = new Image('fake image path');
            $this->assertEquals($expectedAspectRatio, $image->getAspectRatio());
        }

        public function testAspectRatioInfoPortrait()
        {
            $expectedWidth = 300;
            $expectedHeight = 500;
            $expectedAspectRatio = $expectedHeight/$expectedWidth;

            $this->mockImageCreatingFunctions('image/gif');
            $this->mockImageSize($expectedWidth, $expectedHeight);

            $image = new Image('fake image path');
            $this->assertEquals($expectedAspectRatio, $image->getAspectRatio());
        }
        
        public function testAspectRatioSquare()
        {
            $expectedWidth = 100;
            $expectedHeight = 100;
            $expectedAspectRatio = $expectedHeight/$expectedWidth;

            $this->mockImageCreatingFunctions('image/gif');
            $this->mockImageSize($expectedWidth, $expectedHeight);

            $image = new Image('fake image path');
            $this->assertEquals($expectedAspectRatio, $image->getAspectRatio());
        }

    }
}