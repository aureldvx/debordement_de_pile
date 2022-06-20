<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Form\Category\CreateCategoryType;
use App\Form\Category\EditCategoryType;
use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route(path: '/c', name: 'categories_')]
class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly SluggerInterface $slugger,
    ) {
    }

    #[Route(path: '/', name: 'list', methods: ['GET'])]
    public function list(): Response
    {
        return $this->render('public/category/list.index.twig', [
            'categories' => $this->categoryRepository->getAll(),
        ]);
    }

    #[Route(path: '/new', name: 'create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request): Response
    {
        $category = new Category();

        $form = $this
            ->createForm(CreateCategoryType::class, $category)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            $category->setSlug($this->slugger->slug($category->getTitle()));
            $category->setCreatedBy($user);
            $category->setUpdatedBy($user);

            $this->categoryRepository->add($category, true);

            return $this->redirectToRoute('categories_show', ['slug' => $category->getSlug()]);
        }

        return $this->renderForm('public/category/create.html.twig', [
            'category_form' => $form,
        ]);
    }

    #[Route(path: '/{slug}', name: 'show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        if (!$category->isEnabled()) {
            return $this->redirectToRoute('categories_list');
        }

        return $this->render('public/category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route(path: '/{slug}/edit', name: 'edit', methods: ['GET', 'PATCH'])]
    public function edit(Category $category, Request $request): Response
    {
        if (!$this->isGranted('EDIT_CATEGORY', $category)) {
            return $this->redirectToRoute('categories_show', ['slug' => $category->getSlug()]);
        }

        $form = $this
            ->createForm(EditCategoryType::class, $category)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug($this->slugger->slug($category->getTitle()));
            $this->categoryRepository->add($category, true);
            $this->addFlash('success', 'Catégorie mise à jour !');

            return $this->redirectToRoute('categories_show', ['slug' => $category->getSlug()]);
        }

        return $this->renderForm('public/category/edit.html.twig', [
            'category_form' => $form,
            'category' => $category,
        ]);
    }

    #[Route(path: '/{slug}', name: 'delete', methods: ['DELETE'])]
    public function delete(Category $category): Response
    {
        if ($this->isGranted('DELETE_CATEGORY', $category)) {
            $category->setEnabled(false);
            $this->categoryRepository->add($category, true);
        }

        return $this->redirectToRoute('categories_list');
    }
}
