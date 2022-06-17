<?php

namespace App\Entity;

use App\Repository\LoginActivityRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LoginActivityRepository::class)]
class LoginActivity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'loginActivities')]
    #[ORM\JoinColumn(nullable: false)]
    private User $relatedUser;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $connectedAt;

    #[Assert\Ip]
    #[ORM\Column(type: 'binary', length: 16)]
    private int $ipAddress;

    public function getId(): int
    {
        return $this->id;
    }

    public function getRelatedUser(): User
    {
        return $this->relatedUser;
    }

    public function setRelatedUser(User $relatedUser): self
    {
        $this->relatedUser = $relatedUser;

        return $this;
    }

    public function getConnectedAt(): DateTimeImmutable
    {
        return $this->connectedAt;
    }

    public function setConnectedAt(DateTimeImmutable $connectedAt): self
    {
        $this->connectedAt = $connectedAt;

        return $this;
    }

    public function getIpAddress(): string
    {
        return long2ip($this->ipAddress);
    }

    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = ip2long($ipAddress);

        return $this;
    }
}
