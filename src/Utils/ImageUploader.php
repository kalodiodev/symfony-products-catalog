<?php

namespace App\Utils;

use App\Entity\Imageable;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;

class ImageUploader
{
    private $targetDirectory;

    /**
     * ImageUploader constructor.
     *
     * @param $targetDirectory
     */
    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory  . 'products/';
    }

    /**
     * Upload Image
     *
     * @param File $file
     * @param Imageable $parent
     * @return string
     */
    public function upload(File $file, Imageable $parent): string
    {
        $counter = $parent->getImages()->count();
        $filename = $parent->getImageFilenamePrefix() . '-' . $counter . '.' . $file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $filename);
        } catch (FileException $e) {
            // Handle exception
        }

        return $filename;
    }

    private function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}