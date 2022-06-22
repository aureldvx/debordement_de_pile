<?php

namespace App\Controller\Admin;

use App\Repository\CategoryRepository;
use App\Repository\LoginActivityRepository;
use App\Repository\ReportRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    public function __construct(
        private readonly LoginActivityRepository $loginActivityRepository,
        private readonly UserRepository $userRepository,
        private readonly ReportRepository $reportRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly TicketRepository $ticketRepository,
    ) {
    }

    #[Route(path: '/admin', name: 'admin_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        $activities = $this->loginActivityRepository->findBy([], ['connectedAt' => 'DESC'], 10);
        $lastUsers = $this->userRepository->findBy([], ['createdAt' => 'DESC'], 10);
        $lastTickets = $this->ticketRepository->getAll(limit: 13);
        $lastCategories = $this->categoryRepository->findBy([], ['createdAt' => 'DESC'], 10);
        $lastReports = $this->reportRepository->findBy([], ['createdAt' => 'DESC'], 10);

        return $this->render('admin/dashboard.html.twig', [
            'login_activities' => $activities,
            'last_users' => $lastUsers,
            'last_tickets' => $lastTickets,
            'last_categories' => $lastCategories,
            'last_reports' => $lastReports,
        ]);
    }
}
