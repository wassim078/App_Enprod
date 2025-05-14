<?php

// src/Form/ReclamationType.php
// src/Form/ReclamationType.php
namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sujet')
            ->add('description')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    "Technical Issue" => "Technical Issue",
                    "Billing Problem" => "Billing Problem",
                    "Service Complaint" => "Service Complaint",
                    "Other" => "Other"
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('file', VichFileType::class, [
                'required' => false,
                'label' => 'Attachment (PDF or Image - optional)',
                'download_uri' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}