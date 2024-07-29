<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PrivacyController extends AbstractController
{
    #[Route('/privacy', name: 'app_privacy')]
    public function index(): Response
    {
        return $this->render('home/privacy.html.twig', [
            'controller_name' => 'PrivacyController',
        ]);
    }
    #[Route('/conditions-utilisation', name: 'terms_of_service')]
    public function termsOfService(): Response
    {
        return $this->render('home/terms_of_service.html.twig');
    }
}
