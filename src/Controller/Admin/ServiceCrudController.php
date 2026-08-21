<?php
namespace App\Controller\Admin;

use App\Entity\Service;
use App\Service\ServiceImageResizer;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class ServiceCrudController extends AbstractCrudController
{
    public function __construct(private ServiceImageResizer $imageResizer) {}
    public static function getEntityFqcn(): string { return Service::class; }
    public function configureCrud(Crud $crud): Crud { return $crud->setEntityLabelInSingular('Prestation')->setEntityLabelInPlural('Prestations')->setPageTitle(Crud::PAGE_INDEX, 'Catalogue des prestations')->setPageTitle(Crud::PAGE_NEW, 'Ajouter une prestation')->setPageTitle(Crud::PAGE_EDIT, 'Modifier la prestation')->setDefaultSort(['displayOrder' => 'ASC']); }
    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab('Général');
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('code', 'Identifiant')->setHelp('Unique, sans espace. Ex. massage_prenatal');
        yield IntegerField::new('displayOrder', 'Ordre');
        yield MoneyField::new('price', 'Prix')->setCurrency('MRU')->setStoredAsCents(false)->setNumDecimals(0)->setHelp('Laissez vide pour afficher « Sur demande ».');
        yield BooleanField::new('active', 'Publié');
        yield BooleanField::new('featured', 'Sur l’accueil')->setHelp('L’accueil affiche au maximum les 3 premières prestations mises en avant, selon leur ordre.');
        yield ImageField::new('imageOne', 'Image principale')->setBasePath('/uploads/services')->setUploadDir('public/uploads/services')->setUploadedFileNamePattern('[slug]-[timestamp]-[randomhash].[extension]')->setFormTypeOptions(['required' => false, 'attr' => ['accept' => 'image/jpeg,image/png,image/webp']])->setHelp('JPEG, PNG ou WebP. Les images de plus de 1 600 px sont redimensionnées automatiquement.');
        yield ImageField::new('imageTwo', 'Image secondaire')->setBasePath('/uploads/services')->setUploadDir('public/uploads/services')->setUploadedFileNamePattern('[slug]-[timestamp]-[randomhash].[extension]')->setFormTypeOptions(['required' => false, 'attr' => ['accept' => 'image/jpeg,image/png,image/webp']])->setHelp('Facultative. Elle apparaîtra dans la galerie de la prestation.');
        yield FormField::addTab('Français');
        yield TextField::new('titleFr', 'Titre');
        yield TextareaField::new('descriptionFr', 'Description')->hideOnIndex();
        yield FormField::addTab('English');
        yield TextField::new('titleEn', 'Title')->hideOnIndex();
        yield TextareaField::new('descriptionEn', 'Description')->hideOnIndex();
        yield FormField::addTab('العربية');
        yield TextField::new('titleAr', 'العنوان')->hideOnIndex()->setFormTypeOption('attr', ['dir' => 'rtl']);
        yield TextareaField::new('descriptionAr', 'الوصف')->hideOnIndex()->setFormTypeOption('attr', ['dir' => 'rtl']);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
        $this->resizeImages($entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $entityInstance);
        $this->resizeImages($entityInstance);
    }

    private function resizeImages(object $entity): void
    {
        if (!$entity instanceof Service) return;
        $this->imageResizer->resize($entity->getImageOne());
        $this->imageResizer->resize($entity->getImageTwo());
    }
}
