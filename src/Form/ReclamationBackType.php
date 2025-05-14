<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReclamationBackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sujet', TextType::class, [
                'attr' => ['class' => 'form-control', 'readonly' => true],
                'disabled' => true
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => 4, 'readonly' => true],
                'disabled' => true
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    "Technical Issue" => "Technical Issue",
                    "Billing Problem" => "Billing Problem",
                    "Service Complaint" => "Service Complaint",
                    "Other" => "Other"
                ],
                'attr' => ['class' => 'form-select', 'disabled' => true],
                'disabled' => true
            ])
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    "En Attente" => "En Attente",
                    "En Cours" => "En Cours",
                    "Résolue" => "Résolue",
                    "Rejetée" => "Rejetée"
                ],
                'attr' => ['class' => 'form-select']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}