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
use Symfony\Component\Form\CallbackTransformer;
class UserAddForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('image', FileType::class, [
            'label' => 'Profile Image',
            'required' => false,
            'mapped' => false,
            'attr' => ['accept' => 'image/*']
        ])
        ->add('email')
        ->add('plainPassword', PasswordType::class, [
            'mapped' => false,
            'required' => true, // Make password mandatory
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a password',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    'max' => 4096,
                ]),
            ],
        ])
        ->add('adresse', TextType::class, [
            'required' => true,])

        ->add('telephone', TextType::class, [
            'required' => true,])

            
        ->add('roles', ChoiceType::class, [
            'choices' => [
                'Client' => 'ROLE_CLIENT',
                'Entreprise' => 'ROLE_ENTREPRISE',
                'Livreur' => 'ROLE_LIVREUR'
            ],
            'multiple' => false, // Single selection
            'required' => true,
            'label' => 'Role',
            'placeholder' => false // Remove "Choose an option" default
        ]);





        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // Transform array to string
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    // Transform string back to array
                    return [$rolesString];
                }
            ));

}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
