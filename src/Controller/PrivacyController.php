<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PrivacyController extends AbstractController
{
    #[Route('/home', name: 'app_privacy')]
    public function index(): Response
    {
        return $this->render('home/privacy.html.twig', [
            'controller_name' => 'PrivacyController',
        ]);
    }
}
