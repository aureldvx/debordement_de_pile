<?php

namespace App\Form\Ticket;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;

class EditTicketType extends AbstractTicketType
{
    /**
     * @param mixed[] $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setMethod('PATCH');
        $builder = $this->getFields($builder);
        $builder->addEventListener(FormEvents::SUBMIT, [$this, 'onSubmit']);
    }
}
