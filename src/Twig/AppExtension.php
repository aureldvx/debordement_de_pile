<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('display_pseudo', [AppRuntime::class, 'displayPseudo']),
            new TwigFunction('comments_count', [AppRuntime::class, 'getCommentsCountForTicket']),
            new TwigFunction('tickets_count', [AppRuntime::class, 'getTicketsCountForCategory']),
        ];
    }
}
