<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: '`comment`')]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\ManyToOne(targetEntity: Ticket::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private Ticket $ticket;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private Comment $parent;

    /** @var Collection<int, Comment> */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $children;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[ORM\Column(type: 'boolean')]
    private bool $enabled = true;

    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $uuid;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $updatedAt;

    /** @var Collection<int, Vote> */
    #[ORM\OneToMany(mappedBy: 'comment', targetEntity: Vote::class)]
    private Collection $votes;

    /** @var Collection<int, Report> */
    #[ORM\OneToMany(mappedBy: 'comment', targetEntity: Report::class)]
    private Collection $reports;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->reports = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getTicket(): Ticket
    {
        return $this->ticket;
    }

    public function setTicket(Ticket $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $comment): self
    {
        if (!$this->children->contains($comment)) {
            $this->children[] = $comment;
            $comment->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $comment): self
    {
        if ($this->children->removeElement($comment)) {
            if ($comment->getParent() === $this) {
                $comment->setParent(null);
            }
        }

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

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function setUuid($uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Vote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setComment($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->removeElement($vote)) {
            if ($vote->getComment() === $this) {
                $vote->setComment(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setComment($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->removeElement($report)) {
            if ($report->getComment() === $this) {
                $report->setComment(null);
            }
        }

        return $this;
    }
}
