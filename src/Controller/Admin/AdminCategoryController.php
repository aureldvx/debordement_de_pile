<?php

namespace App\Controller\Admin;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/categories', name: 'admin_categories_')]
class AdminCategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    #[Route(path: '/', name: 'list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $offset = max(0, $request->query->getInt('offset'));
        $resolvedOnly = $request->query->getBoolean('active');
        $paginator = $this->categoryRepository->getPaginator(offset: $offset, enabled: $resolvedOnly);

        return $this->render('admin/category/list.html.twig', [
            'categories' => $paginator,
            'previous' => $offset - $this->categoryRepository::ADMIN_MAX_PER_PAGE,
            'next' => min(count($paginator), $offset + $this->categoryRepository::ADMIN_MAX_PER_PAGE),
        ]);
    }
}
