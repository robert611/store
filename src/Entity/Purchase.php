<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PurchaseRepository::class)
 * @ORM\Table(name="`purchase`")
 */
class Purchase
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="purchases")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity=PurchaseProduct::class, mappedBy="purchase", orphanRemoval=true)
     */
    private $purchaseProducts;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_paid;

    public function __construct()
    {
        $this->purchaseProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection|PurchaseProduct[]
     */
    public function getPurchaseProducts(): Collection
    {
        return $this->purchaseProducts;
    }

    public function addPurchaseProduct(PurchaseProduct $purchaseProduct): self
    {
        if (!$this->purchaseProducts->contains($purchaseProduct)) {
            $this->purchaseProducts[] = $purchaseProduct;
            $purchaseProduct->setPurchase($this);
        }

        return $this;
    }

    public function removePurchaseProduct(PurchaseProduct $purchaseProduct): self
    {
        if ($this->purchaseProducts->contains($purchaseProduct)) {
            $this->purchaseProducts->removeElement($purchaseProduct);
            // set the owning side to null (unless already changed)
            if ($purchaseProduct->getPurchase() === $this) {
                $purchaseProduct->setPurchase(null);
            }
        }

        return $this;
    }

    public function getIsPaid(): ?bool
    {
        return $this->is_paid;
    }

    public function setIsPaid(bool $is_paid): self
    {
        $this->is_paid = $is_paid;

        return $this;
    }
}
