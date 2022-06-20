<?php

declare(strict_types=1);

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait SlugTrait
{
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Votre slug doit comprendre au moins {{ limit }} caractères.',
        maxMessage: 'Votre slug doit comprendre au maximum {{ limit }} caractères.'
    )]
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $slug;

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
