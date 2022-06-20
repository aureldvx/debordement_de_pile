<?php

namespace App\Form\Category;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;

class AbstractCategoryType extends AbstractType
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }

    public function onSubmit(FormEvent $event): void
    {
        /** @var Category $category */
        $category = $event->getData();
        $form = $event->getForm();

        $title = $category->getTitle();

        if (empty($title)) {
            $form->get('title')->addError(new FormError('Vous ne pouvez pas créer une catégorie sans nom.'));
        }

        $categoryExists = $this->categoryRepository->findBy(['slug' => $this->slugger->slug($title)]);

        if (count($categoryExists) > 0) {
            $form->get('title')->addError(new FormError('Une catégorie du même nom existe déjà.'));
        }
    }

    protected function getFields(FormBuilderInterface $builder): FormBuilderInterface
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
            ]);

        return $builder;
    }
}
