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
class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => true, // Critical: Must map to the entity's plainPassword
                'label' => 'Password'
            ])

            ->add('adresse', TextType::class, [
                'required' => false, // Set to true if the field is mandatory
                
            ])
            ->add('telephone', TextType::class, [
                'required' => true, // Set to true if the field is mandatory
            ])

            ->add('image', FileType::class, [
                'label' => 'Profile Image',
                'required' => false, // Set to true if the field is mandatory
                 'mapped' => false,// This field is not mapped to the entity directly
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Client' => 'ROLE_CLIENT',
                    'Entreprise' => 'ROLE_ENTREPRISE',
                    'Delivery person' => 'ROLE_LIVREUR',
                ],
                'mapped' => false, // This field is not mapped to the entity directly
                'label' => 'Role',
                 // Optional: Add a placeholder
            ])











        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'validation_groups' => ['Registration'], // Activate Registration group
            'data_class' => User::class,
        ]);
    }
}
