<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends AbstractFixture
{
    protected int $totalToGenerate = 6;

    public function load(ObjectManager $manager): void
    {
        /** @var string[] $pseudos */
        $pseudos = [];

        for ($i = 0; $i < $this->totalToGenerate; ++$i) {
            $user = new User();

            do {
                if (0 === $i) {
                    $pseudo = 'admin';
                } elseif (1 === $i) {
                    $pseudo = 'user';
                } else {
                    $pseudo = $this->faker->userName();
                }
            } while (in_array($pseudo, $pseudos, true));
            $pseudos[] = $pseudo;

            $firstname = $this->faker->firstName();
            $lastname = $this->faker->lastName();

            $user
                ->setPseudo($pseudo)
                ->setPassword($this->passwordHasher->hashPassword($user, 'password'))
                ->setEmail($this->faker->safeEmail())
                ->setFirstname($firstname)
                ->setLastname($lastname)
                ->setSlug($this->slugger->slug($pseudo)->lower())
                ->setAddress($this->faker->streetAddress())
                ->setPostalCode($this->faker->postcode())
                ->setCity($this->faker->city())
                ->setPhone($this->faker->phoneNumber())
                ->setRoles(0 === $i ? ['ROLE_ADMIN'] : []);

            $manager->persist($user);
            $this->addRef('user', $i, $user);
        }

        $manager->flush();
    }
}
