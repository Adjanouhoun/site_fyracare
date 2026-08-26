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
use Doctrine\ORM\EntityManagerInterface;

final class SiteContentCrudController extends AbstractCrudController
{
    public function __construct(private ServiceImageResizer $imageResizer) {}
    public static function getEntityFqcn(): string { return SiteContent::class; }
    public function configureCrud(Crud $crud): Crud { return $crud->setEntityLabelInSingular('Contenu')->setEntityLabelInPlural('Contenus du site')->setPageTitle(Crud::PAGE_INDEX,'Textes & images du site')->setDefaultSort(['page'=>'ASC','code'=>'ASC'])->setSearchFields(['label','code','contentFr','contentEn','contentAr']); }
    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab('Réglages');
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('label','Nom du contenu')->setHelp('Nom lisible dans l’administration.');
        yield TextField::new('code','Clé technique')->setHelp('Ne pas modifier une clé créée automatiquement.')->hideWhenUpdating();
        yield TextField::new('page','Page / rubrique');
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
    public function persistEntity(EntityManagerInterface $em, $entity): void { parent::persistEntity($em,$entity); $this->resize($entity); }
    public function updateEntity(EntityManagerInterface $em, $entity): void { parent::updateEntity($em,$entity); $this->resize($entity); }
    private function resize(object $entity): void { if ($entity instanceof SiteContent) $this->imageResizer->resizeIn('content',$entity->getImage()); }
}
