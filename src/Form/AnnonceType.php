<?php

namespace App\Form;

use App\Entity\Annonce;
use App\Entity\CategorieAnnonce;
use App\Entity\Commande;
use App\Entity\Panier;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('poids')
            ->add('prix')
            ->add('description')
            ->add('image')
            ->add('panier', EntityType::class, [
                'class' => Panier::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('categorieAnnonce', EntityType::class, [
                'class' => CategorieAnnonce::class,
                'choice_label' => 'id',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('commandes', EntityType::class, [
                'class' => Commande::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
