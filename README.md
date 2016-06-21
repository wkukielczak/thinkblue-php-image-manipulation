Image Manipulation Library
===

[![Build Status](https://travis-ci.org/wkukielczak/thinkblue-php-image-manipulation.svg?branch=master)](https://travis-ci.org/wkukielczak/thinkblue-php-image-manipulation)
[![Coverage Status](https://coveralls.io/repos/github/wkukielczak/thinkblue-php-image-manipulation/badge.svg?branch=master)](https://coveralls.io/github/wkukielczak/thinkblue-php-image-manipulation?branch=master)

This is a simple PHP library to make image transformation easy as 1-2-3.
Below you can find a simple example of the library use. For more details, please see the [project's wiki](https://github.com/wkukielczak/thinkblue-php-image-manipulation/wiki "Wiki")

# Example: Image object

    $image = new Image('/path/to/an/image.extension');

# Example: Transform image

    // Add a transformation to the image
    $resize = new \Thinkblue\ImageManipulation\Transformations\Resize();
    $image->addTransformation($resize);
    // Apply and save
    $image->applyTransormations();
    $image->saveTransformedFile('/target/path/name.extension');
