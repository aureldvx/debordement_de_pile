<?php

namespace App\DataFixtures;

use App\Entity\LoginActivity;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use DateTimeImmutable;
use Exception;

class LoginActivityFixtures extends AbstractFixture implements DependentFixtureInterface
{
    protected int $totalToGenerate = 60;

    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < $this->totalToGenerate; ++$i) {
            $activity = new LoginActivity();

            /** @var User $user */
            $user = $this->getRef('user', $i % 6);

            $date = $this->faker->dateTime('now', 'Europe/Paris');

            $activity
                ->setRelatedUser($user)
                ->setIpAddress($this->faker->ipv4())
                ->setConnectedAt(DateTimeImmutable::createFromMutable($date));

            $manager->persist($activity);
        }

        $manager->flush();
    }

    /** @return string[] */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
