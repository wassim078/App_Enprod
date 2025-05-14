<?php

// src/Form/CollectType.php
namespace App\Form;

use App\Entity\Collect;
use App\Entity\CategorieCollect;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Title',
                'attr' => ['class' => 'form-control']
            ])
            ->add('nomProduit', TextType::class, [
                'label' => 'Product Name',
                'attr' => ['class' => 'form-control']
            ])
            ->add('categorieCollect', EntityType::class, [
                'class' => CategorieCollect::class,
                'label' => 'Category',
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-select']
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantity',
                'attr' => ['class' => 'form-control']
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Location',
                'attr' => ['class' => 'form-control']
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Start Date',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Collect::class,
        ]);
    }
}