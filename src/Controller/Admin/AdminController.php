<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Cours;
use App\Entity\Genre;
use App\Entity\Questions;
use App\Entity\Quiz;
use App\Entity\Reponse;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Langue Des Signes Francaise');
    }
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Genre', 'fa-solid fa-venus-mars', Genre::class);
        yield MenuItem::linkToCrud('Category', 'fa-solid fa-list-alt', Category::class);
        yield MenuItem::linkToCrud('Cours', 'fa-solid fa-book', Cours::class);
        yield MenuItem::linkToCrud('Quiz', 'fa-solid fa-question-circle', Quiz::class);
        yield MenuItem::linkToCrud('Questions', 'fa-solid fa-question', Questions::class);
        yield MenuItem::linkToCrud('Reponse', 'fa-solid fa-reply', Reponse::class);
    }
}
