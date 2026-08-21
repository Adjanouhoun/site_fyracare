<?php
namespace App\Controller\Admin;

use App\Entity\AdviceArticle;
use App\Service\ServiceImageResizer;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

final class AdviceArticleCrudController extends AbstractCrudController
{
    public function __construct(private ServiceImageResizer $imageResizer) {}
    public static function getEntityFqcn(): string { return AdviceArticle::class; }
    public function configureCrud(Crud $crud): Crud { return $crud->setEntityLabelInSingular('Conseil')->setEntityLabelInPlural('Conseils')->setPageTitle(Crud::PAGE_INDEX,'Conseils & articles')->setPageTitle(Crud::PAGE_NEW,'Ajouter un conseil')->setPageTitle(Crud::PAGE_EDIT,'Modifier le conseil')->setDefaultSort(['publishedAt'=>'DESC']); }
    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab('Publication');
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('slug','Adresse URL')->setHelp('Minuscules et tirets uniquement. Ex. preparer-accouchement');
        yield ChoiceField::new('category','Catégorie')->setChoices(['Grossesse'=>'pregnancy','Préparation à la naissance'=>'birth-preparation','Allaitement'=>'breastfeeding','Post-partum'=>'postpartum','Santé du périnée'=>'pelvic-health','Bien-être féminin'=>'wellbeing']);
        yield TextField::new('author','Auteur');
        yield DateTimeField::new('publishedAt','Date de publication');
        yield BooleanField::new('published','Publié');
        yield BooleanField::new('featured','Sur l’accueil')->setHelp('L’accueil affiche au maximum trois conseils mis en avant.');
        yield ImageField::new('image','Image principale')->setBasePath('/uploads/articles')->setUploadDir('public/uploads/articles')->setUploadedFileNamePattern('[slug]-[timestamp]-[randomhash].[extension]')->setFormTypeOptions(['required'=>false,'attr'=>['accept'=>'image/jpeg,image/png,image/webp']])->setHelp('JPEG, PNG ou WebP. Les grandes images sont redimensionnées automatiquement.');
        yield FormField::addTab('Français');
        yield TextField::new('titleFr','Titre');
        yield TextareaField::new('excerptFr','Résumé')->hideOnIndex();
        yield TextareaField::new('contentFr','Contenu')->setNumOfRows(16)->hideOnIndex();
        yield TextField::new('seoTitleFr','Titre SEO')->hideOnIndex();
        yield TextareaField::new('seoDescriptionFr','Description SEO')->hideOnIndex();
        yield FormField::addTab('English');
        yield TextField::new('titleEn','Title')->hideOnIndex();
        yield TextareaField::new('excerptEn','Summary')->hideOnIndex();
        yield TextareaField::new('contentEn','Content')->setNumOfRows(16)->hideOnIndex();
        yield TextField::new('seoTitleEn','SEO title')->hideOnIndex();
        yield TextareaField::new('seoDescriptionEn','SEO description')->hideOnIndex();
        yield FormField::addTab('العربية');
        yield TextField::new('titleAr','العنوان')->hideOnIndex()->setFormTypeOption('attr',['dir'=>'rtl']);
        yield TextareaField::new('excerptAr','الملخص')->hideOnIndex()->setFormTypeOption('attr',['dir'=>'rtl']);
        yield TextareaField::new('contentAr','المحتوى')->setNumOfRows(16)->hideOnIndex()->setFormTypeOption('attr',['dir'=>'rtl']);
        yield TextField::new('seoTitleAr','عنوان SEO')->hideOnIndex()->setFormTypeOption('attr',['dir'=>'rtl']);
        yield TextareaField::new('seoDescriptionAr','وصف SEO')->hideOnIndex()->setFormTypeOption('attr',['dir'=>'rtl']);
    }
    public function persistEntity(EntityManagerInterface $em, $entity): void { parent::persistEntity($em,$entity); $this->resize($entity); }
    public function updateEntity(EntityManagerInterface $em, $entity): void { parent::updateEntity($em,$entity); $this->resize($entity); }
    private function resize(object $entity): void { if ($entity instanceof AdviceArticle) $this->imageResizer->resizeIn('articles',$entity->getImage()); }
}
