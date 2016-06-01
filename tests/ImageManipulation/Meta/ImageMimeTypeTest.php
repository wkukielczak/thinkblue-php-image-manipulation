<?php

namespace Thinkblue\ImageManipulation\Meta {

    class ImageMimeTypeTest extends \PHPUnit_Framework_TestCase
    {
        private $imagesTypes = ['image/gif', 'image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png'];
        private $jpgTypes = ['image/jpeg', 'image/jpg', 'image/pjpeg'];
        private $gifTypes = ['image/gif'];
        private $pngTypes = ['image/png'];
        private $commonNotImageMimeTypes = [
            'application/x-bzip2', 'application/json', 'text/css', 'application/msword', 'text/html'
        ];

        public function testShouldBeAnImageMimeType()
        {
            foreach ($this->imagesTypes as $imageType) {
                $this->assertTrue(ImageMimeType::isValidImageMimeType($imageType));
            }
        }

        public function testShouldNotBeAnImageMimeType()
        {
            foreach ($this->commonNotImageMimeTypes as $mimeType) {
                $this->assertFalse(ImageMimeType::isValidImageMimeType($mimeType));
            }
        }

        public function testShouldDetectJpegMimeType()
        {
            foreach ($this->jpgTypes as $jpgType) {
                $this->assertTrue(ImageMimeType::isJpg($jpgType));
            }
        }

        public function testShouldNotDetectJpegMimeType()
        {
            foreach ($this->commonNotImageMimeTypes as $commonNotImageMimeType) {
                $this->assertFalse(ImageMimeType::isJpg($commonNotImageMimeType));
            }
        }

        public function testShouldDetectGifMimeType()
        {
            foreach ($this->gifTypes as $gifType) {
                $this->assertTrue(ImageMimeType::isGif($gifType));
            }
        }

        public function testShouldNotDetectGifMimeType()
        {
            foreach ($this->commonNotImageMimeTypes as $commonNotImageMimeType) {
                $this->assertFalse(ImageMimeType::isGif($commonNotImageMimeType));
            }
        }

        public function testShouldDetectPngMimeType()
        {
            foreach ($this->pngTypes as $pngType) {
                $this->assertTrue(ImageMimeType::isPng($pngType));
            }
        }

        public function testShouldNotDetectPngMimeType()
        {
            foreach ($this->commonNotImageMimeTypes as $commonNotImageMimeType) {
                $this->assertFalse(ImageMimeType::isPng($commonNotImageMimeType));
            }
        }
    }
}
