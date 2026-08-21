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

    #[Route('/{_locale}/prestations/{code}', name: 'app_service_show', requirements: ['_locale' => 'fr|en|ar', 'code' => '[a-z0-9_]+'])]
    public function service(string $code, ServiceRepository $services): Response
    {
        $service = $services->findOneBy(['code' => $code, 'active' => true]);
        if (!$service) {
            throw $this->createNotFoundException();
        }

        $images = [
            'pelvic_rehab' => ['service-4.jpg', 'slide-7.jpg'],
            'birth_support' => ['service-1.jpg', 'slide-8.jpg'],
            'rebozo' => ['service-5.jpg', 'slide-2.jpg'],
            'damp' => ['slide-4.jpg', 'slide-5.jpg'],
            'breastfeeding' => ['service-6.jpg', 'slide-1.jpeg'],
            'perineum' => ['slide-7.jpg', 'slider-8.jpg'],
            'birth_prep' => ['slide-1.jpeg', 'slide-8.jpg'],
            'prenatal_massage' => ['slider-4.jpg', 'slide-3.jpg'],
        ];

        return $this->render('services/show.html.twig', ['service' => $service, 'service_images' => $images[$code] ?? []]);
    }
}
