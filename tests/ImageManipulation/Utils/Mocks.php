<?php

/**
 * Mocks used in test suite
 */
namespace Thinkblue\ImageManipulation {
    
    function file_exists()
    {
        global $fileExists;
        if (isset($fileExists)) {
            return $fileExists;
        } else {
            return call_user_func_array('file_exists', func_get_args());
        }
    }

    function is_file()
    {
        global $isFile;
        if (isset($isFile) && is_bool($isFile)) {
            return $isFile;
        } else {
            return call_user_func_array('is_file', func_get_args());
        }
    }

    function mime_content_type()
    {
        global $mimeContentType;
        if (isset($mimeContentType) && is_string($mimeContentType)) {
            return $mimeContentType;
        } else {
            return call_user_func_array('mime_content_type', func_get_args());
        }
    }

    function getimagesize()
    {
        global $mockGetImageSize;
        global $mockGetImageSizeWidth;
        global $mockGetImageSizeHeight;

        if (isset($mockGetImageSize) && $mockGetImageSize === true) {
            return [
                $mockGetImageSizeWidth,
                $mockGetImageSizeHeight,
                IMAGETYPE_PNG,
                "width=\"$mockGetImageSizeWidth\" height=\"$mockGetImageSizeHeight\"",
                'bits' => 8,
                'mime' => 'image/png'
            ];
        } else {
            return call_user_func_array('getimagesize', func_get_args());
        }
    }
    
}
