<?php

declare(strict_types=1);

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait TitleTrait
{
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Votre titre doit comprendre au moins {{ limit }} caractères.',
        maxMessage: 'Votre titre doit comprendre au maximum {{ limit }} caractères.'
    )]
    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
