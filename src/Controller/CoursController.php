<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\CategoryRepository;
use App\Repository\CoursRepository;
use App\Repository\CommentaireRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoursController extends AbstractController
{
    #[Route('/cours/{userId}/{categoryId?}', name: 'app_cours')]
    public function cours(
        int $userId,
        ?int $categoryId,
        UserRepository $userRepository,
        CoursRepository $coursRepository,
        CategoryRepository $categoryRepository,
        CommentaireRepository $commentaireRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
          // Récupérer l'utilisateur par son ID
          $user = $userRepository->find($userId);
          if (!$user) {
              throw $this->createNotFoundException('Utilisateur non trouvé');
          }

          // Récupérer la catégorie par son ID ou la première catégorie si non spécifié
          if ($categoryId) {
            $category = $categoryRepository->find($categoryId);
            if (!$category) {
                throw $this->createNotFoundException('Catégorie non trouvée');
            }
        } else {
            $category = $categoryRepository->findOneBy([]);
            if (!$category) {
                throw $this->createNotFoundException('Aucune catégorie trouvée');
            }
        }
           // Récupérer les cours et les commentaires de la catégorie
           $cours = $coursRepository->findBy(['category' => $category]);
           $commentaires = $commentaireRepository->findBy(['category' => $category], ['createdAt' => 'DESC']);
   
        // Calculer le grade_count et le grade_total
        $gradeCount = count($commentaires);
        $gradeTotal = 0;
        if ($gradeCount > 0) {
            $sum = array_reduce($commentaires, function ($carry, $commentaire) {
                return $carry + $commentaire->getNote();
            }, 0);
            $gradeTotal = $sum / $gradeCount;
        }

        // Créer un nouveau commentaire
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);

       // Gérer la soumission du formulaire
       if ($request->isMethod('POST') && $request->request->has($form->getName())) {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setCategory($category);
            $commentaire->setUpdatedAt(new \DateTimeImmutable());
            $commentaire->setCreatedAt(new \DateTimeImmutable());
            $commentaire->setUser($user);
            $entityManager->persist($commentaire);
            $entityManager->flush();
            return $this->redirectToRoute('app_cours', ['userId' => $user->getId(), 'categoryId' => $category->getId()]);
        }
    }
        return $this->render('cours/cours.html.twig', [
            'category' => $category,
            'cours' => $cours,
            'commentaires' => $commentaires,
            'gradeCount' => $gradeCount,
            'gradeTotal' => $gradeTotal,
            'form' => $form->createView(),
            'user' =>$user,
        ]);
    }
}

