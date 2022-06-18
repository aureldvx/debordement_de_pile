<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class CommentFixtures extends AbstractFixture implements DependentFixtureInterface
{
    protected int $totalToGenerate = 1080;

    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
        $total = 0;
        for ($i = 0; $i < $this->totalToGenerate; ++$i) {
            /** @var Ticket $ticket */
            $ticket = $this->getRef('ticket', $i % 540);

            $rootComment = $this->createComment($manager, $ticket, $total++);

            $childCount = $this->faker->numberBetween(0, 2);
            for ($j = 0; $j < $childCount; ++$j) {
                $childComment = $this->createComment($manager, $ticket, $total++, $rootComment);

                $subChildCount = $this->faker->numberBetween(0, 2);
                for ($k = 0; $k < $subChildCount; ++$k) {
                    $this->createComment($manager, $ticket, $total++, $childComment);
                }
            }
        }

        $manager->flush();
    }

    /** @return string[] */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TicketFixtures::class,
        ];
    }

    /** @throws Exception */
    protected function createComment(ObjectManager $manager, Ticket $ticket, int $index, ?Comment $parent = null): Comment
    {
        $comment = new Comment();

        /** @var User $user */
        $user = $this->getRef('user', $index % 6);

        $comment
            ->setContent($this->faker->realText(400))
            ->setAuthor($user)
            ->setParent($parent)
            ->setTicket($ticket);

        $manager->persist($comment);
        $this->addRef('comment', $index, $comment);

        return $comment;
    }
}
