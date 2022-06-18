<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class CategoryFixtures extends AbstractFixture implements DependentFixtureInterface
{
    protected int $totalToGenerate = 18;

    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
        /** @var string[] $titles */
        $titles = [];

        for ($i = 0; $i < $this->totalToGenerate; ++$i) {
            $category = new Category();

            do {
                /** @var string $title */
                $title = $this->faker->words(3, true);
            } while (in_array($title, $titles, true));

            /** @var User $user */
            $user = $this->getRef('user', $i % 6);

            $category
                ->setTitle($title)
                ->setSlug($this->slugger->slug($title)->lower())
                ->setCreatedBy($user)
                ->setUpdatedBy($user);

            $manager->persist($category);
            $this->addRef('category', $i, $category);
        }

        $manager->flush();
    }

    /** @return string[] */
    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
