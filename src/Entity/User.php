<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="Istnieje już konto z takim adresem email.")
 * @UniqueEntity(fields={"username"}, message="Istnieje już konto z taką nazwą użytkownika.")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Assert\Length(
     *      min = 6,
     *      max = 32,
     *      minMessage = "Twoje hasło musi mieć przynajmniej {{ limit }} znaków",
     *      maxMessage = "Twoje hasło może mieć maksymalnie {{ limit }} znaków",
     *      allowEmptyString = false
     * )
     */
    private $plainPassword;

    /**
     * @Assert\IsTrue(    
     *      message = "Musisz zaakceptować nasz regulamin",
     * )
     */
    private $agreeTerms;

    /**
     * @Assert\IsTrue(    
     *      message = "Musisz zaakceptować ten warunek",
     * )
     */
    private $agreeDataUsing;

    /**
     * @ORM\Column(type="string", length=128, unique=true)
     * @Assert\Length(
     *      min = 6,
     *      max = 32,
     *      minMessage = "Twoja nazwa użytkownika musi mieć przynajmniej {{ limit }} znaków",
     *      maxMessage = "Twoja nazwa użytkownika może mieć maksymalnie {{ limit }} znaków",
     *      allowEmptyString = false
     * )
     */
    private $username;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="owner", orphanRemoval=true)
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity=Basket::class, mappedBy="user", orphanRemoval=true)
     */
    private $baskets;

    /**
     * @ORM\OneToMany(targetEntity=Purchase::class, mappedBy="user")
     */
    private $purchases;

    /**
     * @ORM\OneToOne(targetEntity=UserAddress::class, mappedBy="user", orphanRemoval=true)
     */
    private $userAddress;

    /**
     * @ORM\OneToMany(targetEntity=Conversation::class, mappedBy="recipient")
     */
    private $conversations;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="author")
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity=ProductOpinion::class, mappedBy="user")
     */
    private $productOpinions;

    /**
     * @ORM\OneToMany(targetEntity=AuctionBid::class, mappedBy="user")
     */
    private $auctionBids;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->baskets = new ArrayCollection();
        $this->purchases = new ArrayCollection();
        $this->conversations = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->productOpinions = new ArrayCollection();
        $this->auctionBids = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setOwner($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getOwner() === $this) {
                $product->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Basket[]
     */
    public function getBaskets(): Collection
    {
        return $this->baskets;
    }

    public function addBasket(Basket $basket): self
    {
        if (!$this->baskets->contains($basket)) {
            $this->baskets[] = $basket;
            $basket->setUser($this);
        }

        return $this;
    }

    public function removeBasket(Basket $basket): self
    {
        if ($this->baskets->contains($basket)) {
            $this->baskets->removeElement($basket);
            // set the owning side to null (unless already changed)
            if ($basket->getUser() === $this) {
                $basket->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Purchase[]
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): self
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases[] = $purchase;
            $purchase->setUser($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): self
    {
        if ($this->purchases->contains($purchase)) {
            $this->purchases->removeElement($purchase);
            // set the owning side to null (unless already changed)
            if ($purchase->getUser() === $this) {
                $purchase->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of userAddress
     */ 
    public function getUserAddress(): ?UserAddress
    {
        return $this->userAddress;
    }

    /**
     * Set the value of userAddress
     *
     * @return  self
     */ 
    public function setUserAddress(?UserAddress $userAddress): self
    {
        $this->userAddress = $userAddress;

        return $this;
    }

    /**
     * @return Collection|Conversation[]
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): self
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations[] = $conversation;
            $conversation->setRecipient($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): self
    {
        if ($this->conversations->contains($conversation)) {
            $this->conversations->removeElement($conversation);
            // set the owning side to null (unless already changed)
            if ($conversation->getRecipient() === $this) {
                $conversation->setRecipient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setAuthor($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getAuthor() === $this) {
                $message->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * Get message = "Musisz zaakceptować nasz regulamin",
     */ 
    public function getAgreeTerms()
    {
        return $this->agreeTerms;
    }

    /**
     * Set message = "Musisz zaakceptować nasz regulamin",
     *
     * @return  self
     */ 
    public function setAgreeTerms($agreeTerms)
    {
        $this->agreeTerms = $agreeTerms;

        return $this;
    }

    /**
     * Get message = "Musisz zaakceptować ten warunek",
     */ 
    public function getAgreeDataUsing()
    {
        return $this->agreeDataUsing;
    }

    /**
     * Set message = "Musisz zaakceptować ten warunek",
     *
     * @return  self
     */ 
    public function setAgreeDataUsing($agreeDataUsing)
    {
        $this->agreeDataUsing = $agreeDataUsing;

        return $this;
    }

    /**
     * Get min = 6,
     */ 
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set min = 6,
     *
     * @return  self
     */ 
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return Collection|ProductOpinion[]
     */
    public function getProductOpinions(): Collection
    {
        return $this->productOpinions;
    }

    public function addProductOpinion(ProductOpinion $productOpinion): self
    {
        if (!$this->productOpinions->contains($productOpinion)) {
            $this->productOpinions[] = $productOpinion;
            $productOpinion->setUser($this);
        }

        return $this;
    }

    public function removeProductOpinion(ProductOpinion $productOpinion): self
    {
        if ($this->productOpinions->removeElement($productOpinion)) {
            // set the owning side to null (unless already changed)
            if ($productOpinion->getUser() === $this) {
                $productOpinion->setUser(null);
            }
        }

        return $this;
    }

    public function hasOpinionOnProduct($product)
    {
        foreach ($this->productOpinions as $opinion)
        {
            if ($opinion->getProduct()->getId() == $product->getId()) return $opinion->getId();
        }

        return null;
    }

    /**
     * @return Collection|AuctionBid[]
     */
    public function getAuctionBids(): Collection
    {
        return $this->auctionBids;
    }

    public function addAuctionBid(AuctionBid $auctionBid): self
    {
        if (!$this->auctionBids->contains($auctionBid)) {
            $this->auctionBids[] = $auctionBid;
            $auctionBid->setUser($this);
        }

        return $this;
    }

    public function removeAuctionBid(AuctionBid $auctionBid): self
    {
        if ($this->auctionBids->removeElement($auctionBid)) {
            // set the owning side to null (unless already changed)
            if ($auctionBid->getUser() === $this) {
                $auctionBid->setUser(null);
            }
        }

        return $this;
    }
}
