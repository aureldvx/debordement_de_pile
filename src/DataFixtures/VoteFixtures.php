<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Entity\User;
use App\Entity\Vote;
use App\Enum\VoteType;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class VoteFixtures extends AbstractFixture implements DependentFixtureInterface
{
    protected int $totalToGenerate = 1150;

    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < $this->totalToGenerate; ++$i) {
            $vote = new Vote();

            /** @var User $user */
            $user = $this->getRef('user', $i % 6);

            $voteType = 0 === $this->faker->numberBetween(0, 1) ? VoteType::UP_VOTE : VoteType::DOWN_VOTE;
            $contentToVote = $this->faker->numberBetween(0, 1);

            $vote
                ->setAuthor($user)
                ->setType($voteType);

            if (0 === $contentToVote) {
                /** @var Ticket $ticket */
                $ticket = $this->getRef('ticket', $i % 540);

                $vote->setTicket($ticket);
            } else {
                /** @var Comment $comment */
                $comment = $this->getRef('comment', $this->faker->numberBetween(0, 1000));

                $vote->setComment($comment);
            }

            $manager->persist($vote);
        }

        $manager->flush();
    }

    /** @return string[] */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TicketFixtures::class,
            CommentFixtures::class,
        ];
    }
}
