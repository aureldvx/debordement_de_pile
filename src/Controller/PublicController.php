<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly TicketRepository $ticketRepository,
    ) {
    }

    #[Route('/', name: 'public_home')]
    public function home(): Response
    {
        $categories = $this->categoryRepository->findBy([], ['createdAt' => 'DESC'], 20);
        $tickets = $this->ticketRepository->getAll(limit: 20);

        return $this->render('public/home.html.twig', [
            'categories' => $categories,
            'tickets' => $tickets,
        ]);
    }
}
