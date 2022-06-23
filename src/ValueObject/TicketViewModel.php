<?php

namespace App\ValueObject;

use App\Entity\Ticket;
use DateTimeImmutable;

class TicketViewModel
{
    public string $uuid = '';

    public string $slug = '';

    public string $pseudo = '';

    public string $title = '';

    public string $content = '';

    public DateTimeImmutable $createdAt;

    public int $upVotesCount = 0;

    public int $downVotesCount = 0;

    public bool $userHasUpvote = false;

    public bool $userHasDownvote = false;

    public bool $userHasReported = false;

    public bool $closed = false;

    public Ticket $entity;
}
