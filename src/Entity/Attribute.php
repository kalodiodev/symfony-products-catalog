<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="attributes")
 * @ORM\Entity(repositoryClass="App\Repository\AttributeRepository")
 * @UniqueEntity(fields={"name"})
 */
class Attribute
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=192)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 2,
     *     max = 80
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *     max = 80
     * )
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductAttribute", mappedBy="attribute", orphanRemoval=true)
     */
    private $productAttributes;

    public function __construct()
    {
        $this->productAttributes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|ProductAttribute[]
     */
    public function getProductAttributes(): Collection
    {
        return $this->productAttributes;
    }

    public function addProductAttribute(ProductAttribute $productAttribute): self
    {
        if (!$this->productAttributes->contains($productAttribute)) {
            $this->productAttributes[] = $productAttribute;
            $productAttribute->setAttribute($this);
        }

        return $this;
    }

    public function removeProductAttribute(ProductAttribute $productAttribute): self
    {
        if ($this->productAttributes->contains($productAttribute)) {
            $this->productAttributes->removeElement($productAttribute);
            // set the owning side to null (unless already changed)
            if ($productAttribute->getAttribute() === $this) {
                $productAttribute->setAttribute(null);
            }
        }

        return $this;
    }
}
