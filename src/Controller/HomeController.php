<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CommentaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CategoryRepository $categoryRepository, CommentaireRepository $commentaireRepository): Response
    {
        $categories=$categoryRepository->findAll();
        $commentaires = $commentaireRepository->findBy(['category' => $categories]);
           // Calculer le grade_count et le grade_total
           $gradeCount = count($commentaires);
           $gradeTotal = 0;
           if ($gradeCount > 0) {
               $sum = array_reduce($commentaires, function ($carry, $commentaire) {
                   return $carry + $commentaire->getNote();
               }, 0);
               $gradeTotal = $sum / $gradeCount;
           }
   
        return $this->render('home/index.html.twig', [
            'categories' =>$categories ,
            'gradeCount' => $gradeCount,
            'gradeTotal' => $gradeTotal,
        ]);
    }
   
}
