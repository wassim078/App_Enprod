<?php

namespace App\Form;

use App\Entity\Collect;
use App\Entity\CategorieCollect;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class CollectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Title'
            ])
            ->add('nomProduit', TextType::class, [
                'label' => 'Product Name'
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Quantity (kg)',
                'html5' => true  // Changed from 'date' => 'true'
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Location'
            ])
            ->add('dateDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Start Date'
            ])
            ->add('categorieCollect', EntityType::class, [
                'class' => CategorieCollect::class,
                'choice_label' => 'nom',
                'label' => 'Category'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Collect::class,
        ]);
    }
}