<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
class UserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'required'=> false,
            ])

            ->add('adresse', TextType::class, [
                'required' => true, // Set to true if the field is mandatory
            ])
            ->add('telephone', TelType::class, [
                'required' => true, // Set to true if the field is mandatory
            ])

            ->add('image', FileType::class, [
                'label' => 'Profile Image',
                'required' => false, // Set to true if the field is mandatory
                 'mapped' => false,// This field is not mapped to the entity directly
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Cient' => 'ROLE_CLIENT',
                    'Entreprise'=> 'ROLE_ENTREPRISE',
                    'Livreur'=> 'ROLE_LIVREUR',
                ],
                'multiple' => true, // Allow multiple roles
                'expanded' => true, // Render as checkboxes
            ])











        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
