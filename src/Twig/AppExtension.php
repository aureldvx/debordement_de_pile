<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('display_pseudo', [AppRuntime::class, 'displayPseudo']),
            new TwigFunction('comments_count', [AppRuntime::class, 'getCommentsCountForTicket']),
            new TwigFunction('tickets_count', [AppRuntime::class, 'getTicketsCountForCategory']),
            new TwigFunction('is_active_page', [AppRuntime::class, 'isActivePage']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('html_decode', 'html_entity_decode'),
        ];
    }
}
