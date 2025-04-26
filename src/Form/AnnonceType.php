<?php

namespace App\Form;

use App\Entity\Annonce;
use App\Entity\CategorieAnnonce;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\CategorieAnnonceRepository;

class   AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('poids')
            ->add('prix')
            ->add('description')
            ->add('imageFile', FileType::class, [
                'label' => 'Annonce Image (JPG/PNG)',
                'mapped' => false,            // no entity property
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id'    => 'imageDropzone' // for Dropzone.js init
                ],
            ])
            ->add('quantite')
            ->add('categorieAnnonce', EntityType::class, [
                'class'        => CategorieAnnonce::class,
                'choice_label' => 'name',
                'placeholder'  => 'Choose a category',
                'query_builder'=> fn(CategorieAnnonceRepository $er) =>
                $er->createQueryBuilder('c')->orderBy('c.name','ASC'),
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
