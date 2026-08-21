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
            'pelvic_rehab' => ['226f02c6dd646446b.jpeg', 'slide-7.jpg'],
            'birth_support' => ['6af3a48ac02540dd3.jpg', 'slide-8.jpg'],
            'rebozo' => ['7db14932e1b17b330.jpg', 'slide-2.jpg'],
            'damp' => ['af46a99ab159ddcab.jpeg', 'slide-5.jpg'],
            'breastfeeding' => ['a3fe5f762701bbc19.jpg', 'slide-1.jpeg'],
            'perineum' => ['b3c15f1f1697818cf.jpg', 'slider-8.jpg'],
            'birth_prep' => ['e9f8633479f26b147.jpeg', 'slide-8.jpg'],
            'prenatal_massage' => ['acb422db38301d0ed.jpg', 'slide-3.jpg'],
        ];

        return $this->render('services/show.html.twig', ['service' => $service, 'service_images' => $images[$code] ?? []]);
    }
}
