<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Entity\Trait\EnabledTrait;
use App\Enum\VoteType;
use App\Helper\DateTimeHelpers;
use App\Repository\VoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
#[ORM\Table(name: '`vote`')]
class Vote
{
    use EnabledTrait;
    use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Ticket::class, cascade: ['persist'], inversedBy: 'votes')]
    private ?Ticket $ticket;

    #[ORM\ManyToOne(targetEntity: Comment::class, cascade: ['persist'], inversedBy: 'votes')]
    private ?Comment $comment;

    #[ORM\Column(type: 'integer')]
    private int $type;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    public function __construct()
    {
        $this->createdAt = DateTimeHelpers::createImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getType(): VoteType
    {
        return VoteType::from($this->type);
    }

    public function setType(VoteType $type): self
    {
        $this->type = $type->value;

        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
