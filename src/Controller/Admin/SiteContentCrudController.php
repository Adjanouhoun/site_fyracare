<?php

namespace App\Controller\Admin;

use App\Entity\SiteContent;
use App\Service\ServiceImageResizer;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Symfony\Component\HttpFoundation\RequestStack;

final class SiteContentCrudController extends AbstractCrudController
{
    public function __construct(private ServiceImageResizer $imageResizer, private RequestStack $requestStack) {}
    public static function getEntityFqcn(): string { return SiteContent::class; }
    public function configureCrud(Crud $crud): Crud { return $crud->setEntityLabelInSingular('Contenu existant')->setEntityLabelInPlural('Contenus des pages')->setPageTitle(Crud::PAGE_INDEX,'Modifier les contenus présents sur le site')->setPageTitle(Crud::PAGE_EDIT, static fn (SiteContent $content) => 'Modifier · '.$content->getLabel())->setDefaultSort(['sitePage'=>'ASC','page'=>'ASC','label'=>'ASC'])->setSearchFields(['label','contentFr','contentEn','contentAr'])->setPaginatorPageSize(30); }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::DELETE, Action::BATCH_DELETE)
            ->update(Crud::PAGE_INDEX, Action::EDIT, static fn (Action $action) => $action->setLabel('Modifier'));
    }
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('sitePage','Page')->setChoices($this->pageChoices()))
            ->add(ChoiceFilter::new('page','Section')->setChoices($this->sectionChoices()))
            ->add(ChoiceFilter::new('type','Type')->setChoices(['Texte'=>SiteContent::TYPE_TEXT,'Image'=>SiteContent::TYPE_IMAGE]));
    }
    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX === $pageName) {
            yield ChoiceField::new('sitePage','Page')->setChoices($this->pageChoices());
            yield ChoiceField::new('page','Section')->setChoices($this->sectionChoices());
            yield TextField::new('label','Élément présent sur la page');
            yield ChoiceField::new('type','Format')->setChoices(['Texte'=>SiteContent::TYPE_TEXT,'Image'=>SiteContent::TYPE_IMAGE]);
            return;
        }

        $content = $this->getContext()?->getEntity()->getInstance();
        if ($content instanceof SiteContent && SiteContent::TYPE_IMAGE === $content->getType()) {
            yield FormField::addTab('Image de la page');
            yield ImageField::new('image','Remplacer l’image')->setBasePath('/uploads/content')->setUploadDir('public/uploads/content')->setUploadedFileNamePattern('[slug]-[timestamp]-[randomhash].[extension]')->setFormTypeOptions(['required'=>false,'attr'=>['accept'=>'image/jpeg,image/png,image/webp']])->setHelp('La nouvelle image sera automatiquement redimensionnée.');
            return;
        }

        yield FormField::addTab('Français');
        yield TextareaField::new('contentFr','Texte')->setNumOfRows(8)->hideOnIndex();
        yield FormField::addTab('English');
        yield TextareaField::new('contentEn','Text')->setNumOfRows(8)->hideOnIndex();
        yield FormField::addTab('العربية');
        yield TextareaField::new('contentAr','النص')->setNumOfRows(8)->hideOnIndex()->setFormTypeOption('attr',['dir'=>'rtl']);
    }
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $sitePage = $this->requestStack->getCurrentRequest()?->query->getString('contentPage');
        if ($sitePage && in_array($sitePage, array_values($this->pageChoices()), true)) {
            $queryBuilder->andWhere('entity.sitePage = :contentPage')->setParameter('contentPage', $sitePage);
        }
        return $queryBuilder;
    }
    public function updateEntity(EntityManagerInterface $em, $entity): void { parent::updateEntity($em,$entity); $this->resize($entity); }
    private function resize(object $entity): void { if ($entity instanceof SiteContent) $this->imageResizer->resizeIn('content',$entity->getImage()); }
    private function pageChoices(): array { return ['Accueil'=>'home','À propos'=>'about','Notre expertise'=>'expertise','Prestations'=>'services','Conseils'=>'advice','Galerie'=>'gallery','Contact'=>'contact','Mentions légales'=>'legal','Navigation & éléments globaux'=>'global','Autres'=>'general']; }
    private function sectionChoices(): array { return ['Introduction'=>'introduction','Contenu principal'=>'contenu','Images'=>'images','Hero'=>'hero','Manifeste'=>'manifesto','Prestations'=>'services','Détail des prestations'=>'details','Éléments de confiance'=>'trust','Fondatrice'=>'founder','Expérience'=>'experience','Galerie'=>'gallery','Avis'=>'testimonials','Réservation'=>'booking','Appel à l’action'=>'appointment','Journal / conseils'=>'journal','Contact'=>'contact','Navigation'=>'nav','Pied de page'=>'footer','Actions'=>'actions','Mentions légales'=>'legal','SEO'=>'seo','Métadonnées'=>'meta','Général'=>'general']; }
}
