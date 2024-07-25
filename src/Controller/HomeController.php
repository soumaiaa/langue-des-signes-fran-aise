<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CommentaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CategoryRepository $categoryRepository, CommentaireRepository $commentaireRepository, Request $request): Response
    {
        $categories = $categoryRepository->findAll();
        $categoryGrades = [];
    
        foreach ($categories as $category) {
            $commentaires = $commentaireRepository->findBy(['category' => $category]);
            $gradeCount = count($commentaires);
            $gradeTotal = 0;
    
            if ($gradeCount > 0) {
                $sum = array_reduce($commentaires, function ($carry, $commentaire) {
                    return $carry + $commentaire->getNote();
                }, 0);
                $gradeTotal = $sum / $gradeCount;
            }
    
            $categoryGrades[] = [
                'category' => $category,
                'gradeCount' => $gradeCount,
                'gradeTotal' => $gradeTotal,
            ];
        }
    
        return $this->render('home/index.html.twig', [
            'categoryGrades' => $categoryGrades,
        ]);
    }
      
}
