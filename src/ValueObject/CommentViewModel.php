<?php

namespace App\ValueObject;

use DateTimeImmutable;

class CommentViewModel
{
    public string $uuid = '';

    public string $pseudo = '';

    public string $content = '';

    public DateTimeImmutable $createdAt;

    public int $upVotesCount = 0;

    public int $downVotesCount = 0;

    public bool $userHasUpvote = false;

    public bool $userHasDownvote = false;

    public bool $userHasReported = false;

    /** @var CommentViewModel[] */
    public array $children = [];
}
