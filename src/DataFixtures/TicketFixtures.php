<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class TicketFixtures extends AbstractFixture implements DependentFixtureInterface
{
    protected int $totalToGenerate = 540;

    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
        /** @var string[] $titles */
        $titles = [];

        for ($i = 0; $i < $this->totalToGenerate; ++$i) {
            $ticket = new Ticket();

            do {
                $title = $this->faker->sentence();
            } while (in_array($title, $titles, true));

            /** @var User $user */
            $user = $this->getRef('user', $i % 6);

            /** @var Category $category */
            $category = $this->getRef('category', $i % 18);

            $ticket
                ->setTitle($title)
                ->setSlug($this->slugger->slug($title)->lower())
                ->setContent($this->faker->realText(800))
                ->setAuthor($user)
                ->setCategory($category);

            $manager->persist($ticket);
            $this->addRef('ticket', $i, $ticket);
        }

        $manager->flush();
    }

    /** @return string[] */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
