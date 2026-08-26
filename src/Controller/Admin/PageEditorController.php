<?php

namespace App\Controller\Admin;

use App\Entity\SiteContent;
use App\Repository\SiteContentRepository;
use App\Service\ServiceImageResizer;
use App\Service\SiteContentSynchronizer;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AdminRoute(path: '/pages/{sitePage}', name: 'page_editor', options: ['methods' => ['GET', 'POST']])]
final class PageEditorController extends AbstractController
{
    private const PAGES = [
        'home'=>'Accueil', 'about'=>'À propos', 'expertise'=>'Notre expertise',
        'services'=>'Page Prestations', 'advice'=>'Page Conseils', 'gallery'=>'Page Galerie',
        'contact'=>'Contact', 'legal'=>'Mentions légales', 'global'=>'En-tête, pied de page & SEO',
    ];
    private const SECTIONS = [
        'announcement'=>'Bandeau supérieur — Accompagnement maternel', 'hero'=>'Bannière — Bienvenue à FyraCare',
        'manifesto'=>'Message de bienvenue', 'services'=>'Nos accompagnements',
        'founder'=>'Notre expertise — Aminata', 'experience'=>'L’expérience FyraCare', 'testimonials'=>'Vos mots — Avis clients',
        'booking'=>'Prise de rendez-vous', 'appointment'=>'Appel à l’action', 'gallery'=>'Présentation de la galerie',
        'journal'=>'Conseils & repères', 'introduction'=>'Introduction de la page', 'contenu'=>'Contenu principal',
        'presentation'=>'Présentation', 'engagements'=>'Engagements et points forts', 'form'=>'Formulaire',
        'details'=>'Détail des prestations', 'trust'=>'Éléments de confiance', 'contact'=>'Coordonnées et formulaire',
        'images'=>'Images de la page', 'nav'=>'Navigation', 'footer'=>'Pied de page', 'actions'=>'Boutons communs',
        'catalog'=>'Catalogue et recherche', 'navigation'=>'Libellés et navigation',
        'seo'=>'Référencement', 'meta'=>'Métadonnées', 'legal'=>'Informations légales', 'general'=>'Identité du site',
    ];
    /** Les groupes correspondent aux blocs réellement rendus dans les templates. */
    private const STRUCTURE = [
        'home'=>[
            'announcement'=>['nav.announcement'],
            'hero'=>['hero.*','journey.*','home.hero_image'], 'manifesto'=>['manifesto.*'],
            'services'=>['services.eyebrow','services.intro','services.title_rich','services.featured_label','services.home_note','services.all'],
            'founder'=>['founder.*','home.founder_image'], 'experience'=>['experience.*','home.experience_image'],
            'journal'=>['advice_page.featured_*'], 'gallery'=>['gallery.*'], 'testimonials'=>['testimonials.*'],
            'booking'=>['booking.*'], 'appointment'=>['appointment.*'],
        ],
        'about'=>[
            'hero'=>['about_page.meta_*','about_page.eyebrow','about_page.title','about_page.intro','about_page.image_alt','about.hero_image'],
            'presentation'=>['about_page.aside','about_page.paragraph_*'],
            'engagements'=>['about_page.point_*'],
            'appointment'=>['about_page.cta_*'],
        ],
        'expertise'=>[
            'hero'=>['expertise_page.meta_*','expertise_page.eyebrow','expertise_page.title','expertise_page.intro','expertise.hero_image'],
            'presentation'=>['expertise_page.aside','expertise_page.paragraph_*'],
            'engagements'=>['expertise_page.skills_*','expertise_page.skill_*'],
            'appointment'=>['expertise_page.cta_*'],
        ],
        'services'=>[
            'introduction'=>['services_page.*','services.hero_image'],
            'catalog'=>['services.catalog_*','services.filter_*','services.price_on_request','services.read_more','services.book','services.no_results'],
            'details'=>['service_detail.*','trust.*'], 'appointment'=>['appointment.*'],
        ],
        'advice'=>[
            'introduction'=>['advice_page.meta_*','advice_page.eyebrow','advice_page.title','advice_page.intro','advice_page.featured_*'],
            'navigation'=>['advice_page.filter_*','advice_page.search_*','advice_page.read','advice_page.all','advice_page.latest','advice_page.author_role','advice_page.no_articles','advice_page.pagination','advice_page.previous','advice_page.next','advice_page.page','advice_page.updated','advice_page.share','advice_page.continue','advice_page.related'],
            'appointment'=>['advice_page.cta_*','advice_page.disclaimer*','appointment.*'],
        ],
        'gallery'=>['introduction'=>['gallery_page.*']],
        'contact'=>[
            'hero'=>['contact.meta_*','contact.eyebrow','contact.title','contact.intro'],
            'contact'=>['contact.details_*','contact.address_*','contact.phone_*','contact.email_*','contact.map'],
            'form'=>['contact.form_*','contact.submit'],
        ],
        'legal'=>['legal'=>['legal.*']],
        'global'=>['general'=>['global.*'],'nav'=>['nav.*'],'footer'=>['footer.*'],'actions'=>['actions.*'],'seo'=>['seo.*','meta.*']],
    ];

    public function __construct(
        private SiteContentRepository $contents,
        private SiteContentSynchronizer $synchronizer,
        private EntityManagerInterface $em,
        private ServiceImageResizer $imageResizer,
        private string $projectDir,
    ) {}

    public function __invoke(string $sitePage, Request $request): Response
    {
        if (!isset(self::PAGES[$sitePage])) throw $this->createNotFoundException();
        $this->synchronizer->synchronize();
        $items = $this->contents->findBy([], ['label'=>'ASC']);
        $groups = [];
        foreach (self::STRUCTURE[$sitePage] as $groupCode => $patterns) {
            foreach ($items as $item) {
                if ($this->matches($item->getCode(), $patterns)) $groups[$groupCode][] = $item;
            }
        }
        $section = $request->query->getString('section');

        if ($section !== '') {
            if (!isset($groups[$section])) throw $this->createNotFoundException();
            if ($request->isMethod('POST')) {
                $this->saveSection($request, $sitePage, $section, $groups[$section]);
                $this->addFlash('success', 'La section a été mise à jour.');
                return $this->redirectToRoute('admin_page_editor', ['sitePage'=>$sitePage,'section'=>$section]);
            }
        }

        return $this->render('admin/page_editor.html.twig', [
            'site_page'=>$sitePage, 'page_label'=>self::PAGES[$sitePage], 'groups'=>$groups,
            'section'=>$section, 'section_label'=>self::SECTIONS[$section] ?? ucfirst($section),
            'section_labels'=>self::SECTIONS,
        ]);
    }

    /** @param SiteContent[] $items */
    private function saveSection(Request $request, string $sitePage, string $section, array $items): void
    {
        if (!$this->isCsrfTokenValid('page_section_'.$sitePage.'_'.$section, $request->request->getString('_token'))) {
            throw $this->createAccessDeniedException('Le formulaire a expiré.');
        }
        $values = $request->request->all('contents');
        $images = $request->files->all('images');
        foreach ($items as $item) {
            $id = (string) $item->getId();
            if (SiteContent::TYPE_TEXT === $item->getType() && isset($values[$id])) {
                $item->setContentFr(trim((string) ($values[$id]['fr'] ?? '')))
                    ->setContentEn(trim((string) ($values[$id]['en'] ?? '')))
                    ->setContentAr(trim((string) ($values[$id]['ar'] ?? '')));
            }
            $file = $images[$id] ?? null;
            if (SiteContent::TYPE_IMAGE === $item->getType() && $file instanceof UploadedFile && $file->isValid()) {
                $extension = $file->guessExtension() ?: 'jpg';
                $filename = sprintf('%s-%s.%s', str_replace(['.','_'],'-',$item->getCode()), bin2hex(random_bytes(6)), $extension);
                $file->move($this->projectDir.'/public/uploads/content', $filename);
                $item->setImage($filename);
                $this->imageResizer->resizeIn('content', $filename);
            }
        }
        $this->em->flush();
    }

    private function matches(string $code, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (str_ends_with($pattern, '*') && str_starts_with($code, substr($pattern, 0, -1))) return true;
            if ($code === $pattern) return true;
        }
        return false;
    }
}
