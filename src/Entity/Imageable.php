<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;

interface Imageable
{
    public function getImages(): Collection;

    public function getImageFilenamePrefix();
}