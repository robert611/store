<?php

namespace App\Entity;

use App\Repository\ProductOpinionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductOpinionRepository::class)
 */
class ProductOpinion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $mark;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $opinion;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $advantages;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $flaws;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="productOpinions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="productOpinions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMark(): ?int
    {
        return $this->mark;
    }

    public function setMark(int $mark): self
    {
        $this->mark = $mark;

        return $this;
    }

    public function getOpinion(): ?string
    {
        return $this->opinion;
    }

    public function setOpinion(?string $opinion): self
    {
        $this->opinion = $opinion;

        return $this;
    }

    public function getAdvantages(): ?string
    {
        return $this->advantages;
    }

    public function setAdvantages(?string $advantages): self
    {
        $this->advantages = $advantages;

        return $this;
    }

    public function getFlaws(): ?string
    {
        return $this->flaws;
    }

    public function setFlaws(?string $flaws): self
    {
        $this->flaws = $flaws;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
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

    public function getFlawsArray()
    {
        $array = explode(',', $this->flaws);

        return $array;
    }

    public function getAdvantagesArray()
    {
        $array = explode(',', $this->advantages);

        return $array;
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
}
