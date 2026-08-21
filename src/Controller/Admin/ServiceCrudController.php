<?php
namespace App\Controller\Admin;

use App\Entity\Service;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class ServiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string { return Service::class; }
    public function configureCrud(Crud $crud): Crud { return $crud->setEntityLabelInSingular('Prestation')->setEntityLabelInPlural('Prestations')->setPageTitle(Crud::PAGE_INDEX, 'Catalogue des prestations')->setPageTitle(Crud::PAGE_NEW, 'Ajouter une prestation')->setPageTitle(Crud::PAGE_EDIT, 'Modifier la prestation')->setDefaultSort(['displayOrder' => 'ASC']); }
    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab('Général');
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('code', 'Identifiant')->setHelp('Unique, sans espace. Ex. massage_prenatal');
        yield IntegerField::new('displayOrder', 'Ordre');
        yield BooleanField::new('active', 'Publié');
        yield BooleanField::new('featured', 'Sur l’accueil')->setHelp('L’accueil affiche au maximum les 3 premières prestations mises en avant, selon leur ordre.');
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
}
