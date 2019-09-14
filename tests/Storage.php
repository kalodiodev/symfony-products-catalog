<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Fake Storage Class
 *
 * @package App\Tests
 */
class Storage {

    /**
     * Create Fake Image file
     *
     * @param $filename
     * @param $extension
     * @param null $folder
     * @param int $width
     * @param int $height
     * @param string $mimeType
     * @return UploadedFile
     */
    public static function createFakeImageFile($filename, $extension, $folder = null, $width = 10, $height = 10, $mimeType = 'image/png'): UploadedFile
    {
        if (! $folder) {
            $folder = sys_get_temp_dir();
        }

        $file = tempnam($folder, 'upl');
        $newFile = $folder . '/' . $filename . '.' . $extension;
        rename($file, $newFile);
        imagepng(imagecreatetruecolor(10, 10), $newFile);
        return new UploadedFile(
            $newFile,
            $filename,
            $mimeType,
            null,
            true
        );
    }

    /**
     * Create fake file
     *
     * @param $filename
     * @param $extension
     * @param null $folder
     * @param string $mimeType
     * @return UploadedFile
     */
    public static function createFakeFile($filename, $extension, $folder = null, $mimeType = 'text/plain')
    {
        if (! $folder) {
            $folder = sys_get_temp_dir();
        }

        $file = tempnam($folder, 'upl');
        $newFile = $folder . '/' . $filename . '.' . $extension;
        rename($file, $newFile);

        return new UploadedFile(
            $newFile,
            $filename,
            $mimeType,
            null,
            true
        );
    }
}