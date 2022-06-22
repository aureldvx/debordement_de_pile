<?php

namespace App\Controller\Admin;

use App\Entity\Report;
use App\Helper\DateTimeHelpers;
use App\Repository\ReportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/reports', name: 'admin_reports_')]
class AdminReportController extends AbstractController
{
    public function __construct(
        private readonly ReportRepository $reportRepository,
    ) {
    }

    #[Route(path: '/', name: 'list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset'));
        $resolvedOnly = $request->query->getBoolean('active');
        $paginator = $this->reportRepository->getPaginator(offset: $offset, enabled: $resolvedOnly);

        return $this->render('admin/report/list.html.twig', [
            'reports' => $paginator,
            'previous' => $offset - $this->reportRepository::MAX_PER_PAGE,
            'next' => min(count($paginator), $offset + $this->reportRepository::MAX_PER_PAGE),
        ]);
    }

    #[Route(path: '/{uuid}', name: 'resolve', methods: ['PATCH'])]
    public function resolve(Report $report): RedirectResponse
    {
        $report->setResolvedAt(DateTimeHelpers::createImmutable());
        $this->reportRepository->add($report, true);

        return $this->redirectToRoute('admin_reports_list', ['active' => false]);
    }
}
