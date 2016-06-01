<?php

namespace Thinkblue\ImageManipulation\Meta;

class ImageMimeType
{

    const GIF = 'gif';
    const PNG = 'png';
    const JPG = 'jpg';

    private static $validImageMimeType = [
        'gif' => ['image/gif'],
        'jpg' => [
            'image/jpeg',
            'image/jpg',
            'image/pjpeg'
        ],
        'png' => ['image/png']
    ];

    /**
     * Check if the given MIME type is valid known image's type
     *
     * @param $mimeType String MIME type to check
     * @return bool
     */
    public static function isValidImageMimeType($mimeType)
    {
        $result = false;
        $lowerCaseMimeType = strtolower($mimeType);
        foreach (self::$validImageMimeType as $type) {
            if (in_array($lowerCaseMimeType, $type)) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    public static function isJpg($mimeType)
    {
        return in_array(strtolower($mimeType), self::$validImageMimeType['jpg']);
    }

    public static function isPng($mimeType)
    {
        return in_array(strtolower($mimeType), self::$validImageMimeType['png']);
    }

    public static function isGif($mimeType)
    {
        return in_array(strtolower($mimeType), self::$validImageMimeType['gif']);
    }

}