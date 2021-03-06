<?php

namespace Thinkblue\ImageManipulation\Utils;

trait TestHelper
{
    private function mockImageCreatingFunctions($forcedMimeType = 'image/png')
    {
        global $mimeContentType;
        global $fileExists;
        global $isFile;

        $fileExists = true;
        $isFile = true;
        $mimeContentType = $forcedMimeType;
    }

    protected function mockImageSize($width, $height)
    {
        global $mockGetImageSize;
        global $mockGetImageSizeWidth;
        global $mockGetImageSizeHeight;

        $mockGetImageSize = true;
        $mockGetImageSizeWidth = $width;
        $mockGetImageSizeHeight = $height;
    }

    protected function createImage($fileName = 'img.jpg', $width = 100, $height = 100)
    {
        $tmpDir = sys_get_temp_dir();
        $targetDir = null;
        $img = imagecreatetruecolor($width, $height);

        $targetDir = $tmpDir . '/' . $fileName;
        $pathInfo = pathinfo($targetDir);
        $extension = $pathInfo['extension'];

        switch ($extension) {
            case 'png':
                imagepng($img, $targetDir, 0);
                break;
            case 'gif':
                imagegif($img, $targetDir);
                break;

            case 'jpg':
            default:
                imagejpeg($img, $targetDir, 100);
                break;
        }

        return $targetDir;
    }
}