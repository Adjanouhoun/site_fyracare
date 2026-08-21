<?php
namespace App\Controller\Admin;

use App\Entity\Service;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Tab;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class ServiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string { return Service::class; }
    public function configureCrud(Crud $crud): Crud { return $crud->setEntityLabelInSingular('Prestation')->setEntityLabelInPlural('Prestations')->setDefaultSort(['displayOrder' => 'ASC']); }
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('code', 'Identifiant')->setHelp('Unique, sans espace. Ex. massage_prenatal');
        yield IntegerField::new('displayOrder', 'Ordre');
        yield BooleanField::new('active', 'Publié');
        yield BooleanField::new('featured', 'Sur l’accueil');
        yield Tab::add('Français');
        yield TextField::new('titleFr', 'Titre');
        yield TextareaField::new('descriptionFr', 'Description');
        yield Tab::add('English');
        yield TextField::new('titleEn', 'Title');
        yield TextareaField::new('descriptionEn', 'Description');
        yield Tab::add('العربية');
        yield TextField::new('titleAr', 'العنوان')->setFormTypeOption('attr', ['dir' => 'rtl']);
        yield TextareaField::new('descriptionAr', 'الوصف')->setFormTypeOption('attr', ['dir' => 'rtl']);
    }
}
