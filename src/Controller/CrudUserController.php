<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/crud/user')]
class CrudUserController extends AbstractController
{
    #[Route('/{id}/Profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, FileUploader  $fileUploader,
     EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $encodedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($encodedPassword);
            }
            $this->createMedia($form, 'photo', $fileUploader, $user);
            
            $entityManager->flush();
           
            return $this->redirectToRoute('app_profile', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('crud_user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_crud_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home');

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
