<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;


    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: Message::class, cascade: ['persist'])]
    private $messages;
    
    #[ORM\Column(type: 'text')]
    private $lastMessageContent;

    #[ORM\Column(type: 'datetime_immutable')]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'conversations')]
    #[ORM\JoinColumn(nullable: true)]
    private $user;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $interlocutor;


    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $this->lastMessageContent = $message->getContent();
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

        return $this;
    }


    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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

    public function getInterlocutor(): ?User
    {
        return $this->interlocutor;
    }

    public function setInterlocutor(?User $interlocutor): self
    {
        $this->interlocutor = $interlocutor;

        return $this;
    }


    /**
     * Get the value of lastMessageContent
     */ 
    public function getLastMessageContent()
    {
        return $this->lastMessageContent;
    }

    /**
     * Set the value of lastMessageContent
     *
     * @return  self
     */ 
    public function setLastMessageContent($lastMessageContent)
    {
        $this->lastMessageContent = $lastMessageContent;

        return $this;
    }
}
