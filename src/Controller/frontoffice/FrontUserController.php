<?php

namespace App\Controller\frontoffice;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
class FrontUserController extends AbstractController
{
    #[Route('templatefrontoffice/profil/edit', name: 'user_profile_edit')]
    public function editProfile(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        SluggerInterface $slugger
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $uploadedFile = $request->files->get('profile_image');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('image')->getData();

                    if ($imageFile) {
                        // Generate unique filename
                     $newFilename = $slugger->slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME))
                        .'-'.uniqid().'.'.$imageFile->guessExtension();

                            // Move file
                        $imageFile->move(
                            $this->getParameter('images_directory'),
                             $newFilename
                            );
    
                            // Update user entity
                            $user->setImage($newFilename);
                            }
                    // Handle password change
                    if (!empty($form->get('plainPassword')->getData())) {
                        $user->setPassword(
                        $passwordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                }
                 $entityManager->flush();
                $this->addFlash('success', 'Profile updated successfully!');
                return $this->redirectToRoute('user_profile_edit');
            }

        return $this->render('templatefrontoffice/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
