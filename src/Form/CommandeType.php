<?php

// src/Form/CommandeType.php
namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('client_name', TextType::class, [
                'label' => 'Prénom *',
                'attr' => ['class' => 'form-control-lg'],
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 255])
                ]
            ])
            ->add('client_family_name', TextType::class, [
                'label' => 'Nom *',
                'attr' => ['class' => 'form-control-lg'],
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 255])
                ]
            ])
            ->add('client_adresse', TextType::class, [
                'label' => 'Adresse complète *',
                'attr' => ['class' => 'form-control-lg'],
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 255])
                ]
            ])
            ->add('client_phone', TextType::class, [
                'label' => 'Téléphone *',
                'attr' => [
                    'class' => 'form-control-lg',
                    'placeholder' => 'Ex: 12345678'
                ],
                'constraints' => [
                    new NotBlank(),
                    new Regex(['pattern' => '/^[0-9]{8}$/', 'message' => 'Numéro invalide (8 chiffres requis)'])
                ]
            ])
            ->add('methode_paiement', ChoiceType::class, [
                'label' => 'Méthode de paiement *',
                'choices' => [
                    'Paiement à la livraison' => 'à la livraison',
                    'E-Paiement' => 'E-paiement'
                ],
                'expanded' => true,
                'attr' => ['class' => 'payment-method-choices'],
                'constraints' => [new NotBlank()]
            ])
            ->add('instruction_speciale', TextareaType::class, [
                'label' => 'Instructions spéciales',
                'required' => false,
                'attr' => [
                    'class' => 'form-control-lg',
                    'rows' => 3,
                    'placeholder' => 'Informations supplémentaires pour la livraison...'
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