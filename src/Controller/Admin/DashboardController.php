<?php
namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response { return $this->redirectToRoute('admin_service_index'); }
    public function configureDashboard(): Dashboard { return Dashboard::new()->setTitle('FyraCare · Administration')->setFaviconPath('images/logo-fyracare.png'); }
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkTo(ServiceCrudController::class, 'Prestations', 'fa fa-heart');
        yield MenuItem::linkToRoute('Voir le site', 'fa fa-arrow-up-right-from-square', 'app_home', ['_locale' => 'fr']);
        yield MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out');
    }
}
