<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=2048)
     */
    private $descritpion;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=ProductBasicProperty::class, mappedBy="product")
     */
    private $productBasicProperties;

    /**
     * @ORM\OneToMany(targetEntity=ProductPhysicalProperty::class, mappedBy="product")
     */
    private $productPhysicalProperties;

    /**
     * @ORM\OneToMany(targetEntity=ProductSpecificProperty::class, mappedBy="product")
     */
    private $productSpecificProperties;

    public function __construct()
    {
        $this->productPhysicalProperties = new ArrayCollection();
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

    public function getDescritpion(): ?string
    {
        return $this->descritpion;
    }

    public function setDescritpion(string $descritpion): self
    {
        $this->descritpion = $descritpion;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|ProductPhysicalProperty[]
     */
    public function getProductPhysicalProperties(): Collection
    {
        return $this->productPhysicalProperties;
    }

    public function addProductPhysicalProperty(ProductPhysicalProperty $productPhysicalProperty): self
    {
        if (!$this->productPhysicalProperties->contains($productPhysicalProperty)) {
            $this->productPhysicalProperties[] = $productPhysicalProperty;
            $productPhysicalProperty->setProduct($this);
        }

        return $this;
    }

    public function removeProductPhysicalProperty(ProductPhysicalProperty $productPhysicalProperty): self
    {
        if ($this->productPhysicalProperties->contains($productPhysicalProperty)) {
            $this->productPhysicalProperties->removeElement($productPhysicalProperty);
            // set the owning side to null (unless already changed)
            if ($productPhysicalProperty->getProduct() === $this) {
                $productPhysicalProperty->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductBasicProperty[]
     */
    public function getProductBasicProperties(): Collection
    {
        return $this->productBasicProperties;
    }

    /**
     * @return Collection|ProductSpecificProperty[]
     */
    public function getProductSpecificProperties(): Collection
    {
        return $this->productSpecificProperties;
    }
}
