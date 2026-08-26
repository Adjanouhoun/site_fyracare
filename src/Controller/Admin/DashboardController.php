<?php
namespace App\Controller\Admin;

use App\Entity\Appointment;
use App\Entity\ContactMessage;
use App\Entity\Testimonial;
use App\Service\SiteContentSynchronizer;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(private EntityManagerInterface $entityManager, private SiteContentSynchronizer $contentSynchronizer) {}
    public function index(): Response
    {
        $this->contentSynchronizer->synchronize();
        return $this->render('admin/dashboard.html.twig', [
            'pendingAppointments' => $this->entityManager->getRepository(Appointment::class)->count(['status' => Appointment::STATUS_PENDING]),
            'upcomingAppointments' => $this->entityManager->getRepository(Appointment::class)->count(['status' => Appointment::STATUS_CONFIRMED]),
            'newMessages' => $this->entityManager->getRepository(ContactMessage::class)->count(['status' => ContactMessage::STATUS_NEW]),
            'pendingTestimonials' => $this->entityManager->getRepository(Testimonial::class)->count(['status' => Testimonial::STATUS_PENDING]),
        ]);
    }
    public function configureDashboard(): Dashboard { return Dashboard::new()->setTitle('FyraCare · Administration')->setFaviconPath('images/logo-fyracare.png'); }
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::section('Contenu éditorial');
        yield MenuItem::linkTo(SiteContentCrudController::class, 'Textes & images', 'fa fa-pen-to-square');
        yield MenuItem::linkTo(GalleryItemCrudController::class, 'Galerie photo / vidéo', 'fa fa-photo-film');
        yield MenuItem::linkTo(ServiceCrudController::class, 'Prestations', 'fa fa-heart');
        yield MenuItem::linkTo(AdviceArticleCrudController::class, 'Conseils', 'fa fa-newspaper');
        yield MenuItem::linkTo(AppointmentCrudController::class, 'Rendez-vous', 'fa fa-calendar-check');
        yield MenuItem::linkTo(AvailabilityCrudController::class, 'Disponibilités', 'fa fa-clock');
        yield MenuItem::linkTo(ContactMessageCrudController::class, 'Messages', 'fa fa-envelope');
        yield MenuItem::linkTo(TestimonialCrudController::class, 'Avis clients', 'fa fa-star');
        yield MenuItem::linkToRoute('Voir le site', 'fa fa-arrow-up-right-from-square', 'app_home', ['_locale' => 'fr']);
        yield MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out');
    }
}
