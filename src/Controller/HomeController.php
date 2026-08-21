<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/{_locale}', name: 'app_home', requirements: ['_locale' => 'fr|en|ar'], defaults: ['_locale' => 'fr'])]
    public function index(ServiceRepository $services): Response
    {
        return $this->render('home/index.html.twig', ['featured_services' => $services->findFeatured(), 'services' => $services->findActive()]);
    }

    #[Route('/{_locale}/prestations', name: 'app_services', requirements: ['_locale' => 'fr|en|ar'])]
    public function services(ServiceRepository $services): Response
    {
        return $this->render('services/index.html.twig', ['services' => $services->findActive()]);
    }
}
