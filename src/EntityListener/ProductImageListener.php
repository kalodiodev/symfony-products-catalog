<?php

namespace App\EntityListener;

use App\Entity\ProductImage;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ProductImageListener
{
    protected $projectDirectory;

    public function __construct($projectDirectory)
    {
        $this->projectDirectory = $projectDirectory;
    }

    public function postRemove(ProductImage $productImage, LifecycleEventArgs $event)
    {
        unlink($this->projectDirectory . '/public' . $productImage->getPath());
    }
}