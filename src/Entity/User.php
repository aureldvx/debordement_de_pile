<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Entity\Trait\EnabledTrait;
use App\Entity\Trait\SlugTrait;
use App\Entity\Trait\UpdatedAtTrait;
use App\Entity\Trait\UuidTrait;
use App\Helper\DateTimeHelpers;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('pseudo', message: 'Votre pseudo est déjà utilisé par un autre utilisateur.')]
#[UniqueEntity('slug')]
#[UniqueEntity('uuid')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use EnabledTrait;
    use UuidTrait;
    use SlugTrait;
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[Assert\Length(
        min: 4,
        max: 180,
        minMessage: 'Votre pseudo doit comprendre au moins {{ limit }} caractères.',
        maxMessage: 'Votre pseudo doit comprendre au maximum {{ limit }} caractères.'
    )]
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $pseudo;

    /** @var string[] $roles */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'Votre prénom doit comprendre au moins {{ limit }} caractères.',
        maxMessage: 'Votre prénom doit comprendre au maximum {{ limit }} caractères.'
    )]
    #[ORM\Column(type: 'string', length: 100)]
    private string $firstname;

    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'Votre nom doit comprendre au moins {{ limit }} caractères.',
        maxMessage: 'Votre nom doit comprendre au maximum {{ limit }} caractères.'
    )]
    #[ORM\Column(type: 'string', length: 100)]
    private string $lastname;

    #[ORM\Column(type: 'string', length: 255)]
    private string $address;

    #[Assert\Length(
        min: 2,
        max: 10,
        minMessage: 'Votre code postal doit comprendre au moins {{ limit }} caractères.',
        maxMessage: 'Votre code postal doit comprendre au maximum {{ limit }} caractères.'
    )]
    #[ORM\Column(type: 'string', length: 10)]
    private string $postalCode;

    #[Assert\Length(
        min: 2,
        max: 200,
        minMessage: 'Votre ville doit comprendre au moins {{ limit }} caractères.',
        maxMessage: 'Votre ville doit comprendre au maximum {{ limit }} caractères.'
    )]
    #[ORM\Column(type: 'string', length: 200)]
    private string $city;

    #[Assert\Email]
    #[Assert\Length(
        min: 2,
        max: 200,
        minMessage: 'Votre email doit comprendre au moins {{ limit }} caractères.',
        maxMessage: 'Votre email doit comprendre au maximum {{ limit }} caractères.'
    )]
    #[ORM\Column(type: 'string', length: 200)]
    private string $email;

    #[Assert\Length(
        min: 10,
        max: 20,
        minMessage: 'Votre numéro de téléphone doit comprendre au moins {{ limit }} caractères.',
        maxMessage: 'Votre numéro de téléphone doit comprendre au maximum {{ limit }} caractères.'
    )]
    #[ORM\Column(type: 'string', length: 20)]
    private string $phone;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $blockedAt = null;

    /** @var Collection<int, Ticket> */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Ticket::class, orphanRemoval: true)]
    private Collection $tickets;

    /** @var Collection<int, Comment> */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    /** @var Collection<int, Vote> */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Vote::class, orphanRemoval: true)]
    private Collection $votes;

    /** @var Collection<int, Report> */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Report::class)]
    private Collection $reports;

    /** @var Collection<int, LoginActivity> */
    #[ORM\OneToMany(mappedBy: 'relatedUser', targetEntity: LoginActivity::class)]
    private Collection $loginActivities;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->loginActivities = new ArrayCollection();
        $this->uuid = Uuid::v4();
        $this->createdAt = DateTimeHelpers::createImmutable();
        $this->updatedAt = DateTimeHelpers::createClassic();
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = DateTimeHelpers::createClassic();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->pseudo;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /** @param string[] $roles */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getBlockedAt(): ?DateTimeImmutable
    {
        return $this->blockedAt;
    }

    public function setBlockedAt(?DateTimeImmutable $blockedAt): self
    {
        $this->blockedAt = $blockedAt;

        return $this;
    }

    /** @return Collection<int, Ticket> */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setAuthor($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        $this->tickets->removeElement($ticket);

        return $this;
    }

    /** @return Collection<int, Comment> */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        $this->comments->removeElement($comment);

        return $this;
    }

    /** @return Collection<int, Vote> */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setAuthor($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        $this->votes->removeElement($vote);

        return $this;
    }

    /** @return Collection<int, Report> */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setAuthor($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        $this->reports->removeElement($report);

        return $this;
    }

    /** @return Collection<int, LoginActivity> */
    public function getLoginActivities(): Collection
    {
        return $this->loginActivities;
    }

    public function addLoginActivity(LoginActivity $loginActivity): self
    {
        if (!$this->loginActivities->contains($loginActivity)) {
            $this->loginActivities[] = $loginActivity;
            $loginActivity->setRelatedUser($this);
        }

        return $this;
    }

    public function removeLoginActivity(LoginActivity $loginActivity): self
    {
        $this->loginActivities->removeElement($loginActivity);

        return $this;
    }
}
