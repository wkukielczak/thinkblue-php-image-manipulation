<?php

namespace Thinkblue\ImageManipulation;

use Thinkblue\ImageManipulation\Transformations\AbstractTransformation;
use Thinkblue\ImageManipulation\Meta\ImageMimeType;

/**
 * Class Image
 * @package Thinkblue\ImageManipulationBundle
 *
 * Simple image file representation
 */
class Image
{
    /**
     * @var String
     */
    private $fileFullPath;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string
     */
    private $fileExtension;

    /**
     * @var float
     */
    private $aspectRatio = 0.0;

    /**
     * @var resource
     */
    private $fileResource;

    /**
     * @var resource|null
     */
    private $transformedFileResource = null;

    /**
     * @var array
     */
    private $transformations = [];

    /**
     * Image constructor.
     * @param $fileFullPath String Directory of the image file
     */
    public function __construct($fileFullPath)
    {
        if (file_exists($fileFullPath) && is_file($fileFullPath) && $this->isImage($fileFullPath)) {
            $this->fileFullPath = $fileFullPath;
        } else {
            throw new \InvalidArgumentException('You must use image\'s correct path to create the Image object');
        }
    }

    /**
     * Check if the file is of known image format
     *
     * @param $fileFullPath String Directory of the image file
     * @return bool Returns true for the correct image MIME type and false for the incorrect one
     */
    private function isImage($fileFullPath)
    {
        $this->mimeType = mime_content_type($fileFullPath);
        return ImageMimeType::isValidImageMimeType($this->mimeType);
    }

    /**
     * Get image sizes on demand
     */
    private function getImageSizes()
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($width, $height, $type, $attr) = getimagesize($this->fileFullPath);
        $this->width = $width;
        $this->height = $height;
    }

    private function getPathInfo()
    {
        $pathParts = pathinfo($this->fileFullPath);
        $this->filePath = $pathParts['dirname'];
        $this->fileName = $pathParts['basename'];
        $this->fileExtension = $pathParts['extension'];
    }

    /**
     * Get the MIME type of the image
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Get the image's width
     *
     * @return int
     */
    public function getWidth()
    {
        if (null === $this->width) {
            $this->getImageSizes();
        }

        return $this->width;
    }

    /**
     * Get the image's height
     *
     * @return int
     */
    public function getHeight()
    {
        if (null === $this->height) {
            $this->getImageSizes();
        }

        return $this->height;
    }

    /**
     * Get image aspect ratio
     *
     * @return float Image aspect ratio
     */
    public function getAspectRatio()
    {
        if (0 == $this->aspectRatio) {
            if ($this->getWidth() > $this->getHeight()) {
                $this->aspectRatio = $this->getWidth() / $this->getHeight();
            } else if ($this->getWidth() < $this->getHeight()) {
                $this->aspectRatio = $this->getHeight() / $this->getWidth();
            } else {
                $this->aspectRatio = 1.0;
            }
        }

        return $this->aspectRatio;
    }

    /**
     * Get name of the file (contains file extension)
     *
     * @return string
     */
    public function getFileName()
    {
        if (null === $this->fileName) {
            $this->getPathInfo();
        }

        return $this->fileName;
    }

    /**
     * Get file's directory
     *
     * @return string
     */
    public function getFilePath()
    {
        if (null === $this->filePath) {
            $this->getPathInfo();
        }

        return $this->filePath;
    }

    /**
     * Get file's extension
     *
     * return string
     */
    public function getFileExtension()
    {
        if (null === $this->fileExtension) {
            $this->getPathInfo();
        }

        return $this->fileExtension;
    }

    /**
     * Get image resource
     *
     * @return resource
     */
    public function getImageResource()
    {
        if (null === $this->fileResource) {
            if (ImageMimeType::isGif($this->mimeType)) {
                $this->fileResource = imagecreatefromgif($this->fileFullPath);
            }

            if (ImageMimeType::isJpg($this->mimeType)) {
                $this->fileResource = imagecreatefromjpeg($this->fileFullPath);
            }

            if (ImageMimeType::isPng($this->mimeType)) {
                $this->fileResource = imagecreatefrompng($this->fileFullPath);
            }
        }

        return $this->fileResource;
    }

    /**
     * Add transformation to this image
     *
     * @param AbstractTransformation $transformation
     */
    public function addTransformation(AbstractTransformation $transformation)
    {
        if (!in_array($transformation, $this->transformations)) {
            $this->transformations[] = $transformation;
        }
    }

    /**
     * Apply all the requested transformations. Will return an array with the transformations report so the developer
     * has the exact feedback of what was done and what not in case of failure in one of the transformations. Sample
     * output:
     * [
     *  'Resize' => true,
     *  'SetAlpha' => false
     * ]
     *
     * @return array
     */
    public function applyTransformations()
    {
        $summary = [];

        if (!empty($this->transformations)) {
            /** @var $transformation AbstractTransformation */
            foreach ($this->transformations as $transformation) {
                $transformation->setImage($this);
                $output = $transformation->apply();
                $success = false;

                if (null != $output) {
                    $success = true;
                    $this->transformedFileResource = $output;
                }

                // Save the summary
                $summary[$transformation->getTransformationName()] = $success;
            }
        }

        return $summary;
    }

    /**
     * If you applied some transformations and wat to save a copy of the image, use this method providing target
     * filename and (optional) compression level.
     *
     * @param $filename string  Output filename
     * @param int $compression Compression level
     * @return null|resource
     */
    public function saveTransformedFile($filename, $compression = 0)
    {
        if (ImageMimeType::isGif($this->mimeType)) {
            imagegif($this->transformedFileResource, $filename);
        }

        if (ImageMimeType::isJpg($this->mimeType)) {
            // No compression by default
            if ($compression == 0) $compression = 100;
            imagejpeg($this->transformedFileResource, $filename, $compression);
        }

        if (ImageMimeType::isPng($this->mimeType)) {
            imagepng($this->transformedFileResource, $filename, $compression);
        }

        return $this->transformedFileResource;
    }

    /**
     * Release all the resources
     */
    function __destruct()
    {
        if (null != $this->fileResource) {
            imagedestroy($this->fileResource);
        }
        if (null != $this->transformedFileResource) {
            imagedestroy($this->transformedFileResource);
        }
    }
    
}
