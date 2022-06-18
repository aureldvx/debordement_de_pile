<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Entity\Trait\EnabledTrait;
use App\Entity\Trait\UuidTrait;
use App\Helper\DateTimeHelpers;
use App\Repository\ReportRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
#[ORM\Table(name: '`report`')]
class Report
{
    use EnabledTrait;
    use CreatedAtTrait;
    use UuidTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\ManyToOne(targetEntity: Ticket::class, inversedBy: 'reports')]
    private ?Ticket $ticket;

    #[ORM\ManyToOne(targetEntity: Comment::class, inversedBy: 'reports')]
    private ?Comment $comment;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $resolvedAt;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
        $this->createdAt = DateTimeHelpers::createImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
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

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getResolvedAt(): ?DateTimeImmutable
    {
        return $this->resolvedAt;
    }

    public function setResolvedAt(?DateTimeImmutable $resolvedAt): self
    {
        $this->resolvedAt = $resolvedAt;

        return $this;
    }
}
