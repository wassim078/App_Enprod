<?php

// src/Form/CommandeBackType.php
namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeBackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('etatCommande', ChoiceType::class, [
                'label' => 'Order Status',
                'choices' => [
                    'PAID' => 'PAID',
                    'AWAITING_DELIVERY' => 'AWAITING_DELIVERY',
                    'PENDING' => 'PENDING',
                    'PROCESSING' => 'PROCESSING',
                    'SHIPPED' => 'SHIPPED',
                    'DELIVERED' => 'DELIVERED',
                    'CANCELLED' => 'CANCELLED'
                ],
                'attr' => [
                    'class' => 'form-select',
                    'style' => 'max-width: 200px;'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}