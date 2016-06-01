<?php

namespace Thinkblue\ImageManipulation\Transformations;

use Thinkblue\ImageManipulation\Meta\ImageMimeType;

/**
 * Class ResizeTransformation
 * @package Thinkblue\ImageManipulationBundle\Transformation
 *
 * Resize the image
 */
class ResizeTransformation extends AbstractTransformation
{
    /**
     * @var int
     */
    private $maxWidth;

    /**
     * @var int
     */
    private $maxHeight;

    /**
     * @var bool
     */
    private $preserveAspectRatio = true;

    /**
     * @var bool
     */
    private $preservePngTransparency = true;

    /**
     * @return boolean
     */
    public function isPreservePngTransparency()
    {
        return $this->preservePngTransparency;
    }

    /**
     * @param boolean $preservePngTransparency
     * @return $this
     */
    public function setPreservePngTransparency($preservePngTransparency)
    {
        $this->preservePngTransparency = $preservePngTransparency;
        return $this;
    }

    /**
     * @param $maxWidth int Image's desired width
     * @return $this
     */
    public function setMaxWidth($maxWidth)
    {
        $this->validateDimensionValue($maxWidth);
        $this->maxWidth = $maxWidth;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxWidth()
    {
        return $this->maxWidth;
    }

    /**
     * @param $maxHeight int Image's desired height
     * @return $this
     */
    public function setMaxHeight($maxHeight)
    {
        $this->validateDimensionValue($maxHeight);
        $this->maxHeight = $maxHeight;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxHeight()
    {
        return $this->maxHeight;
    }

    /**
     * @param $value bool Set whether to preserve image's aspect ratio or not
     * @return $this
     */
    public function setPreserveAspectRatio($value)
    {
        if (is_bool($value)) {
            $this->preserveAspectRatio = $value;
        } else {
            throw new \InvalidArgumentException('You must use value of bool type');
        }
        return $this;
    }

    /**
     * @return boolean
     */
    public function isPreserveAspectRatio()
    {
        return $this->preserveAspectRatio;
    }

    /**
     * Validate if given dimension is INT > 0
     * Throws an InvalidArgumentException in case value is incorrect
     *
     * @param $value
     */
    private function validateDimensionValue($value)
    {
        if (is_int($value) && $value > 0) {
            return;
        }
        throw new \InvalidArgumentException('Image\'s dimension must be an int greater than 0');
    }

    /**
     * Count target dimensions basing on given MAX values
     *
     * @return array
     * @throws \Exception
     */
    private function countTargetDimensions()
    {
        $imageRatio = $this->getImage()->getAspectRatio();

        if (null != $this->getMaxHeight() && null === $this->getMaxWidth() && $this->isPreserveAspectRatio()) {
            $targetWidth = $this->getMaxHeight() * $imageRatio;
            $targetHeight = $this->getMaxHeight();
        } else if (null != $this->getMaxWidth() && null === $this->getMaxHeight() && $this->isPreserveAspectRatio()) {
            $targetWidth = $this->getMaxWidth();
            $targetHeight = $this->getMaxWidth() * $imageRatio;
        } else if (!$this->isPreserveAspectRatio()) {
            $targetWidth = $this->getMaxWidth();
            $targetHeight = $this->getMaxHeight();
        } else {
            throw new \Exception('Use case not yet covered by the Transformation class');
        }

        return [round($targetWidth), round($targetHeight)];
    }

    /**
     * This method should apply the transformation to the image object
     *
     * @return resource Returns image resource after applying transformation, null otherwise
     * @throws \Exception
     */
    public function apply()
    {
        // Get original image information
        $originalWidth = $this->getImage()->getWidth();
        $originalHeight = $this->getImage()->getHeight();
        $imageResource = $this->getImage()->getImageResource();

        // Break if there is not enough arguments
        if (null == $this->getMaxHeight() && null == $this->getMaxWidth()) {
            throw new \InvalidArgumentException('You have to set minimum width or height for the new image');
        }

        list($targetWidth, $targetHeight) = $this->countTargetDimensions();

        // Copy the image file:
        $targetHeight = round($targetHeight);
        $targetWidth = round($targetWidth);

        $newImageResource = imagecreatetruecolor($targetWidth, $targetHeight);

        if (ImageMimeType::isPng($this->getImage()->getMimeType()) && $this->isPreservePngTransparency()) {
            imagealphablending($newImageResource, false);
            imagesavealpha($newImageResource, true);
        }

        $success = imagecopyresampled(
            $newImageResource,
            $imageResource,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $originalWidth,
            $originalHeight
        );

        if (!$success) {
            $newImageResource = null;
        }

        return $newImageResource;
    }

    /**
     * Name of the transformation
     *
     * @return string
     */
    public function getTransformationName()
    {
        return 'Resize';
    }
}
