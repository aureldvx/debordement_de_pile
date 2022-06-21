<?php

namespace App\Form\Ticket;

use App\Entity\Category;
use App\Entity\Ticket;
use App\Repository\TicketRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AbstractTicketType extends AbstractType
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly TicketRepository $ticketRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }

    public function onSubmit(FormEvent $event): void
    {
        /** @var Ticket $ticket */
        $ticket = $event->getData();
        $form = $event->getForm();

        $title = $ticket->getTitle();

        $ticketsExists = $this->ticketRepository->findBy(['slug' => $this->slugger->slug($title)]);

        if (count($ticketsExists) > 0) {
            $form->get('title')->addError(
                new FormError(
                    sprintf(
                        'Un ticket existe déjà avec ce titre, vous devriez peut-être voir ses réponses avant d\'en créer un nouveau : <a href="%s">%s</a>',
                        $this->urlGenerator->generate('tickets_show', ['slug' => $ticketsExists[0]->getSlug()], $this->urlGenerator::ABSOLUTE_URL),
                        $ticketsExists[0]->getTitle(),
                    )
                )
            );
        }
    }

    protected function getFields(FormBuilderInterface $builder): FormBuilderInterface
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Titre',
            ])
            ->add('content', TextareaType::class, [
                'required' => true,
                'label' => 'Descriptif',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'label' => 'Catégorie rattachée',
                'multiple' => false,
                'expanded' => false,
            ]);

        return $builder;
    }
}
