<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Report;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class ReportFixtures extends AbstractFixture implements DependentFixtureInterface
{
    protected int $totalToGenerate = 200;

    /** @throws Exception */
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < $this->totalToGenerate; ++$i) {
            $report = new Report();

            /** @var User $user */
            $user = $this->getRef('user', $i % 6);

            $report
                ->setAuthor($user)
                ->setDescription($this->faker->realText());

            $contentToReport = $this->faker->numberBetween(0, 1);

            if (0 === $contentToReport) {
                /** @var Ticket $ticket */
                $ticket = $this->getRef('ticket', $i % 540);

                $report->setTicket($ticket);
            } else {
                /** @var Comment $comment */
                $comment = $this->getRef('comment', $this->faker->numberBetween(0, 1000));

                $report->setComment($comment);
            }

            $manager->persist($report);
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
