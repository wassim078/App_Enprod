<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'disabled' => true
            ])

            ->add('plainPassword', PasswordType::class, [
                'required' => false,
                ])

            ->add('adresse', TextType::class, [
                'required'   => false,
                'empty_data' => '',  // Forces empty submissions to be an empty string
            ])
            ->add('telephone', TextType::class, [
                'required'   => false,
                'empty_data' => '',
                'attr' => [
                'class' => 'form-control',
                'autocomplete' => 'tel' // Instructs the browser to treat this as a telephone input
        ],
            ])
            
              
            ->add('image', FileType::class, [
                'label' => false,
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'd-none']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => ['EditUser'],
            'data_class' => User::class,
        ]);
    }
}
