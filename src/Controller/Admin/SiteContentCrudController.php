<?php

namespace App\Controller\Admin;

use App\Entity\SiteContent;
use App\Service\ServiceImageResizer;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
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
    public function configureCrud(Crud $crud): Crud { return $crud->setEntityLabelInSingular('Contenu')->setEntityLabelInPlural('Contenus du site')->setPageTitle(Crud::PAGE_INDEX,'Contenus classés par page et section')->setDefaultSort(['sitePage'=>'ASC','page'=>'ASC','label'=>'ASC'])->setSearchFields(['label','code','contentFr','contentEn','contentAr'])->setPaginatorPageSize(30); }
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('sitePage','Page')->setChoices($this->pageChoices()))
            ->add(ChoiceFilter::new('page','Section')->setChoices($this->sectionChoices()))
            ->add(ChoiceFilter::new('type','Type')->setChoices(['Texte'=>SiteContent::TYPE_TEXT,'Image'=>SiteContent::TYPE_IMAGE]));
    }
    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab('Réglages');
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('label','Nom du contenu')->setHelp('Nom lisible dans l’administration.');
        yield TextField::new('code','Clé technique')->setHelp('Ne pas modifier une clé créée automatiquement.')->hideWhenUpdating();
        yield ChoiceField::new('sitePage','Page')->setChoices($this->pageChoices());
        yield ChoiceField::new('page','Section')->setChoices($this->sectionChoices());
        yield ChoiceField::new('type','Type')->setChoices(['Texte'=>SiteContent::TYPE_TEXT,'Image'=>SiteContent::TYPE_IMAGE]);
        yield BooleanField::new('active','Actif');
        yield ImageField::new('image','Image')->setBasePath('/uploads/content')->setUploadDir('public/uploads/content')->setUploadedFileNamePattern('[slug]-[timestamp]-[randomhash].[extension]')->setFormTypeOptions(['required'=>false,'attr'=>['accept'=>'image/jpeg,image/png,image/webp']])->setHelp('Utilisé uniquement pour un contenu de type Image.');
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
    public function persistEntity(EntityManagerInterface $em, $entity): void { parent::persistEntity($em,$entity); $this->resize($entity); }
    public function updateEntity(EntityManagerInterface $em, $entity): void { parent::updateEntity($em,$entity); $this->resize($entity); }
    private function resize(object $entity): void { if ($entity instanceof SiteContent) $this->imageResizer->resizeIn('content',$entity->getImage()); }
    private function pageChoices(): array { return ['Accueil'=>'home','À propos'=>'about','Notre expertise'=>'expertise','Prestations'=>'services','Conseils'=>'advice','Galerie'=>'gallery','Contact'=>'contact','Mentions légales'=>'legal','Navigation & éléments globaux'=>'global','Autres'=>'general']; }
    private function sectionChoices(): array { return ['Introduction'=>'introduction','Contenu principal'=>'contenu','Images'=>'images','Hero'=>'hero','Manifeste'=>'manifesto','Prestations'=>'services','Détail des prestations'=>'details','Éléments de confiance'=>'trust','Fondatrice'=>'founder','Expérience'=>'experience','Galerie'=>'gallery','Avis'=>'testimonials','Réservation'=>'booking','Appel à l’action'=>'appointment','Journal / conseils'=>'journal','Contact'=>'contact','Navigation'=>'nav','Pied de page'=>'footer','Actions'=>'actions','Mentions légales'=>'legal','SEO'=>'seo','Métadonnées'=>'meta','Général'=>'general']; }
}
