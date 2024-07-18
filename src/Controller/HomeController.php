<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CommentaireRepository;
use App\Service\CookieManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    private $cookieManager;

    public function __construct(CookieManager $cookieManager)
    {
        $this->cookieManager = $cookieManager;
    }

    #[Route('/', name: 'app_home')]
    public function index(CategoryRepository $categoryRepository, CommentaireRepository $commentaireRepository,  Request $request): Response
    {
        // Vérifier le consentement aux cookies
        $hasConsented = $this->cookieManager->hasConsented($request);

        $categories = $categoryRepository->findAll();
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
            'categories' => $categories,
            'gradeCount' => $gradeCount,
            'gradeTotal' => $gradeTotal,
            'hasConsented' => $hasConsented
        ]);
    }

   
    #[Route('/consent', name: 'consent_page', methods: ['POST'])]
    public function consent(Request $request): Response
    {
        $this->cookieManager->setCookie('consented', time() + (365 * 24 * 60 * 60)); // Expire dans un an
        return $this->redirectToRoute('app_home');
    }
}
