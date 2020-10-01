<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=1024)
     */
    private $text;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity=Conversation::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $conversation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

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

    public function getFormattedDate()
    {
        $date = $this->created_at->format('j') . " ";
        $month = $this->created_at->format('n');

        switch ($month) {
            case 1: 
                $date .= "Stycznia";
                break;
            case 2: 
                $date .= "Lutego"; 
                break;
            case 3: 
                $date .= "Marca";
                break;
            case 4: 
                $date .= "Kwietnia";
                break;
            case 5: 
                $date .= "Maja";
                break;
            case 6: 
                $date .= "Czerwca";
                break;
            case 7: 
                $date .= "Lipca";
                break;
            case 8: 
                $date .= "Sierpńa";
                break;
            case 9: 
                $date .= "Września";
                break;
            case 10: 
                $date .= "Października";
                break;
            case 11: 
                $date .= "Listopada";
                break;
            case 12: 
                $date .= "Grudnia";
        }

        return $date .= " " . $this->created_at->format('Y');
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }
}
