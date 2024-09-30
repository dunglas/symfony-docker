<?php

namespace App\Controller\Admin;

use App\Entity\Bus;
use App\Entity\Photo;
use App\Entity\Line;
use App\Entity\Incident;
use App\Entity\IncidentPhoto;
use App\Entity\User;


use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        //return $this->redirect($adminUrlGenerator->setController(DashboardController::class)->generateUrl()); // à conserver
        return $this->render('admin/dashboard.html.twig');

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('App');
    }

    
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Bus', 'fas fa-car', Bus::class);
        yield MenuItem::linkToCrud('Photos', 'fas fa-google', Photo::class);
        yield MenuItem::linkToCrud('Lines', 'fas fa-list', Line::class);
        yield MenuItem::linkToCrud('Incidents', 'fas fa-comment', Incident::class);
        yield MenuItem::linkToCrud('Incidents Photos', 'fas fa-tags', IncidentPhoto::class);
        yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);

        //yield MenuItem::linkToCrud('Line', 'fas fa-user', Line::class);

    }
    
}
