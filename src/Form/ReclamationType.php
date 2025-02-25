<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Reclamation;
use Doctrine\ORM\EntityManagerInterface; // Correct namespace
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ReclamationType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) // Correct type hint
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Plainte' => 'Plainte',
                    'Demande information' => 'Demande information',
                    'Suggestion' => 'Suggestion',
                ],
                'placeholder' => 'Sélectionnez un type',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez sélectionner un type de réclamation.']),
                ],
                'data' => 'Plainte', // Valeur par défaut
            ])

            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getNom() . ' ' . $user->getPrenom();
                },
                'placeholder' => 'Sélectionnez un utilisateur',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez sélectionner un utilisateur.']),
                ],
            ])

            ->add('sujet', TextType::class, [
                'attr' => [
                    'placeholder' => 'Sujet',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 20,
                        'minMessage' => 'Le sujet doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le sujet ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])

            ->add('description', TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 100,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])

            ->add('file', FileType::class, [
                'required' => false,
                'data_class' => null,
                'label' => 'Veuillez insérer des documents liés à votre réclamation',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])

            ->add('submitButton', SubmitType::class, [
                'label' => 'Envoyer la réclamation',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}