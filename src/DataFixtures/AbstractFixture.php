<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Exception;
use Faker\Factory;
use Faker\Generator;
use Faker\Provider\fr_FR\PhoneNumber;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

abstract class AbstractFixture extends Fixture
{
    protected readonly Generator $faker;

    protected int $totalToGenerate = 5;

    public function __construct(
        protected SluggerInterface $slugger,
        protected UserPasswordHasherInterface $passwordHasher,
    ) {
        $this->faker = Factory::create('fr_FR');
        $this->faker->addProvider(new PhoneNumber($this->faker));
    }

    protected function addRef(string $name, int $index, object $object): void
    {
        $this->addReference("{$name}_{$index}", $object);
    }

    /** @throws Exception */
    protected function getRef(string $name, int $index): object
    {
        $ref = "{$name}_{$index}";

        if ($this->hasReference($ref)) {
            return $this->getReference($ref);
        }

        throw new Exception('No fixture reference exists with these name and index.');
    }
}
