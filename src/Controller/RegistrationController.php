<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\FileUploader;
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request,FileUploader $fileUploader, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                    $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $this->createMedia($form, 'photo', $fileUploader, $user);

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
         $this->addFlash('success', 'Registration successful. You can now log in.');

                return $this->redirectToRoute('app_login');
            } else {
                // Handle any unexpected errors during registration
                $this->addFlash('error', 'An error occurred during registration.');
            }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
    private function createMedia($form, string $formInput, FileUploader  $fileUploader, User $user)
    {
        $mediaFile = $form->get($formInput)->getData();
        if ($mediaFile) {
            $media = new Media();
            $mediaFileName = $fileUploader->upload($mediaFile);
            $media->setUrl($mediaFileName);
            $method = "set" . ucfirst($formInput);
            $user->$method($media);
        }
    }
}
