<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\ContactMessage;
use App\Entity\Testimonial;
use App\Form\AppointmentType;
use App\Form\ContactMessageType;
use App\Form\TestimonialType;
use App\Repository\ServiceRepository;
use App\Repository\AdviceArticleRepository;
use App\Repository\TestimonialRepository;
use App\Service\AdminNotificationMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/{_locale}', name: 'app_home', requirements: ['_locale' => 'fr|en|ar'], defaults: ['_locale' => 'fr'])]
    public function index(Request $request, ServiceRepository $services, TestimonialRepository $testimonials, AdviceArticleRepository $articles): Response
    {
        $locale = $request->getLocale();
        $form = $this->createForm(TestimonialType::class, new Testimonial(), [
            'locale' => $locale,
            'action' => $this->generateUrl('app_testimonial_submit', ['_locale' => $locale]),
            'method' => 'POST',
        ]);
        $appointment = new Appointment();
        $serviceCode = $request->query->getString('service');
        if ($serviceCode !== '') {
            $selectedService = $services->findOneBy(['code' => $serviceCode, 'active' => true]);
            if ($selectedService) {
                $appointment->setService($selectedService);
            }
        }
        $appointmentForm = $this->createForm(AppointmentType::class, $appointment, ['locale' => $locale, 'action' => $this->generateUrl('app_appointment_submit', ['_locale' => $locale])]);
        return $this->render('home/index.html.twig', ['featured_services' => $services->findFeatured(), 'services' => $services->findActive(), 'featured_articles' => $articles->findFeatured(), 'testimonials' => $testimonials->findApproved(), 'testimonial_form' => $form, 'appointment_form' => $appointmentForm]);
    }

    #[Route('/{_locale}/rendez-vous', name: 'app_appointment_submit', requirements: ['_locale' => 'fr|en|ar'], methods: ['POST'])]
    public function submitAppointment(string $_locale, Request $request, EntityManagerInterface $entityManager, AdminNotificationMailer $notificationMailer): Response
    {
        $appointment = new Appointment();
        $form = $this->createForm(AppointmentType::class, $appointment, ['locale' => $_locale]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $slot = $appointment->getAvailability();
            if ($slot && $slot->isActive() && $slot->getStartsAt() > new \DateTimeImmutable()) {
                $slot->setActive(false);
                $appointment->setStatus(Appointment::STATUS_PENDING);
                $entityManager->persist($appointment);
                $entityManager->flush();
                $notificationMailer->newAppointment($appointment);
                $this->addFlash('appointment_success', 'booking.success');
            } else {
                $this->addFlash('appointment_error', 'booking.slot_unavailable');
            }
        } else {
            $this->addFlash('appointment_error', 'booking.error');
        }
        return $this->redirectToRoute('app_home', ['_locale' => $_locale, '_fragment' => 'reservation'], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{_locale}/contact', name: 'app_contact', requirements: ['_locale' => 'fr|en|ar'], methods: ['GET', 'POST'])]
    public function contact(string $_locale, Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = new ContactMessage();
        $form = $this->createForm(ContactMessageType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && (string) $form->get('website')->getData() === '') {
            $entityManager->persist($message);
            $entityManager->flush();
            $this->addFlash('contact_success', 'contact.success');
            return $this->redirectToRoute('app_contact', ['_locale' => $_locale], Response::HTTP_SEE_OTHER);
        }
        return $this->render('contact/index.html.twig', ['contact_form' => $form]);
    }

    #[Route('/{_locale}/a-propos', name: 'app_about', requirements: ['_locale' => 'fr|en|ar'])]
    public function about(): Response { return $this->render('about/index.html.twig'); }

    #[Route('/{_locale}/notre-expertise', name: 'app_expertise', requirements: ['_locale' => 'fr|en|ar'])]
    public function expertise(): Response { return $this->render('expertise/index.html.twig'); }

    #[Route('/{_locale}/confidentialite', name: 'app_privacy', requirements: ['_locale' => 'fr|en|ar'])]
    public function privacy(): Response { return $this->render('legal/privacy.html.twig'); }

    #[Route('/{_locale}/mentions-legales', name: 'app_legal', requirements: ['_locale' => 'fr|en|ar'])]
    public function legal(): Response { return $this->render('legal/legal.html.twig'); }

    #[Route('/sitemap.xml', name: 'app_sitemap', defaults: ['_format' => 'xml'])]
    public function sitemap(ServiceRepository $services, AdviceArticleRepository $articles): Response
    {
        return $this->render('seo/sitemap.xml.twig', ['services' => $services->findActive(), 'articles' => $articles->findPublished()], new Response('', 200, ['Content-Type' => 'application/xml']));
    }

    #[Route('/robots.txt', name: 'app_robots')]
    public function robots(): Response { return new Response("User-agent: *\nAllow: /\nDisallow: /admin\nSitemap: /sitemap.xml\n", 200, ['Content-Type' => 'text/plain']); }

    #[Route('/{_locale}/avis', name: 'app_testimonial_submit', requirements: ['_locale' => 'fr|en|ar'], methods: ['POST'])]
    public function submitTestimonial(string $_locale, Request $request, EntityManagerInterface $entityManager, AdminNotificationMailer $notificationMailer): Response
    {
        $testimonial = new Testimonial();
        $form = $this->createForm(TestimonialType::class, $testimonial, ['locale' => $_locale]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $testimonial->setStatus(Testimonial::STATUS_PENDING);
            $entityManager->persist($testimonial);
            $entityManager->flush();
            $notificationMailer->newTestimonial($testimonial);
            $this->addFlash('testimonial_success', 'testimonials.success');
        } else {
            $this->addFlash('testimonial_error', 'testimonials.error');
        }
        return $this->redirectToRoute('app_home', ['_locale' => $_locale, '_fragment' => 'testimonials'], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{_locale}/prestations', name: 'app_services', requirements: ['_locale' => 'fr|en|ar'])]
    public function services(ServiceRepository $services): Response
    {
        return $this->render('services/index.html.twig', ['services' => $services->findActive()]);
    }

    #[Route('/{_locale}/conseils', name: 'app_advice', requirements: ['_locale' => 'fr|en|ar'])]
    public function advice(Request $request, AdviceArticleRepository $articles): Response
    {
        $categories = ['birth-preparation', 'wellbeing', 'pelvic-health', 'breastfeeding', 'postpartum'];
        $selectedCategory = $request->query->getString('categorie');
        if (!in_array($selectedCategory, $categories, true)) {
            $selectedCategory = '';
        }
        $search = trim(mb_substr($request->query->getString('q'), 0, 80));
        $page = max(1, $request->query->getInt('page', 1));
        $perPage = 6;
        $total = $articles->countPublished($selectedCategory ?: null, $search);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $pages);
        return $this->render('advice/index.html.twig', ['articles' => $articles->searchPublished($selectedCategory ?: null, $search, $perPage, ($page - 1) * $perPage), 'categories' => $categories, 'selected_category' => $selectedCategory, 'search' => $search, 'current_page' => $page, 'total_pages' => $pages, 'total_articles' => $total]);
    }

    #[Route('/{_locale}/conseils/rss.xml', name: 'app_advice_rss', requirements: ['_locale' => 'fr|en|ar'], defaults: ['_format' => 'xml'])]
    public function adviceRss(AdviceArticleRepository $articles): Response
    {
        return $this->render('advice/rss.xml.twig', ['articles' => $articles->findPublished()], new Response('', 200, ['Content-Type' => 'application/rss+xml; charset=UTF-8']));
    }

    #[Route('/{_locale}/conseils/{slug}', name: 'app_advice_show', requirements: ['_locale' => 'fr|en|ar', 'slug' => '[a-z0-9-]+'])]
    public function adviceShow(string $slug, AdviceArticleRepository $articles): Response
    {
        $article = $articles->findPublishedBySlug($slug);
        if (!$article) {
            throw $this->createNotFoundException();
        }

        return $this->render('advice/show.html.twig', ['article' => $article, 'related_articles' => $articles->findRelated($article)]);
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
