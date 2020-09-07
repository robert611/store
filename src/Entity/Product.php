<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

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
    private $description;

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
     * @ORM\JoinColumn(nullable=false)
     */
    private $productBasicProperties;

    /**
     * @ORM\OneToMany(targetEntity=ProductPhysicalProperty::class, mappedBy="product")
     * @ORM\JoinColumn(nullable=false)
     */
    private $productPhysicalProperties;

    /**
     * @ORM\OneToMany(targetEntity=ProductSpecificProperty::class, mappedBy="product")
     */
    private $productSpecificProperties;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull(message="Wybierz którąś z opcji")
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull(message="Wybierz którąś z opcji")
     */
    private $auction_type;

    /**
     * @ORM\ManyToMany(targetEntity=DeliveryType::class, mappedBy="product")
     */
    private $deliveryTypes;

    /**
     * @ORM\OneToMany(targetEntity=ProductPicture::class, mappedBy="product", orphanRemoval=true)
     */
    private $productPictures;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $delivery_time;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    public function __construct()
    {
        $this->productPhysicalProperties = new ArrayCollection();
        $this->deliveryTypes = new ArrayCollection();
        $this->productPictures = new ArrayCollection();
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

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getAuctionType(): ?string
    {
        return $this->auction_type;
    }

    public function setAuctionType(string $auction_type): self
    {
        $this->auction_type = $auction_type;

        return $this;
    }

    /**
     * @return Collection|DeliveryType[]
     */
    public function getDeliveryTypes(): Collection
    {
        return $this->deliveryTypes;
    }

    public function addDeliveryType(DeliveryType $deliveryType): self
    {
        if (!$this->deliveryTypes->contains($deliveryType)) {
            $this->deliveryTypes[] = $deliveryType;
            $deliveryType->addProduct($this);
        }

        return $this;
    }

    public function removeDeliveryType(DeliveryType $deliveryType): self
    {
        if ($this->deliveryTypes->contains($deliveryType)) {
            $this->deliveryTypes->removeElement($deliveryType);
            $deliveryType->removeProduct($this);
        }

        return $this;
    }

    /**
     * @return Collection|ProductPicture[]
     */
    public function getProductPictures(): Collection
    {
        return $this->productPictures;
    }

    public function addProductPicture(ProductPicture $productPicture): self
    {
        if (!$this->productPictures->contains($productPicture)) {
            $this->productPictures[] = $productPicture;
            $productPicture->setProduct($this);
        }

        return $this;
    }

    public function removeProductPicture(ProductPicture $productPicture): self
    {
        if ($this->productPictures->contains($productPicture)) {
            $this->productPictures->removeElement($productPicture);
            // set the owning side to null (unless already changed)
            if ($productPicture->getProduct() === $this) {
                $productPicture->setProduct(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getDeliveryTime(): ?string
    {
        return $this->delivery_time;
    }

    public function setDeliveryTime(string $delivery_time): self
    {
        $this->delivery_time = $delivery_time;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCheapestDeliveryPrice()
    {
        $min = $this->deliveryTypes[0]->getDefaultPrice();

        foreach($this->deliveryTypes as $delivery)
        {
            if ($min > $delivery->getDefaultPrice()) $min = $delivery->getDefaultPrice();
        }

        return $min;
    }
}
