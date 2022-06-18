<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        /** @var string[] $pseudos */
        $pseudos = [];

        $references = new ReferenceValueObject();

        for ($i = 0; $i <= $this->totalToGenerate; ++$i) {
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
                ->setSlug($this->slugger->slug($firstname.' '.$lastname)->lower())
                ->setAddress($this->faker->streetAddress())
                ->setPostalCode($this->faker->postcode())
                ->setCity($this->faker->city())
                ->setPhone($this->faker->phoneNumber())
                ->setRoles(0 === $i ? ['ROLE_ADMIN'] : []);

            $manager->persist($user);
            $references->addValue($user);
        }

        $this->addReference('users', $references);
        $manager->flush();
    }
}
