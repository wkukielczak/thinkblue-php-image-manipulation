<?php

namespace Thinkblue\ImageManipulation\Transformation;

use Thinkblue\ImageManipulation\Image;

abstract class AbstractTransformation
{
    /**
     * @var Image
     */
    protected $image;

    /**
     * To apply the transformation to the image, add the image reference so the transformation class will have full
     * access to the image
     *
     * @param Image $image Image object to apply transformation to
     */
    public function setImage(Image $image)
    {
        $this->image = $image;
    }

    /**
     * @return Image Image object which the transformation is related to
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * This method should apply the transformation to the image object
     *
     * @return resource Returns image resource after applying transformation, null otherwise
     */
    abstract public function apply();

    /**
     * Get human-friendly name of the transformation. Will be used for image's transformations summary
     *
     * @return string
     */
    abstract public function getTransformationName();
}
