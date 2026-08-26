<?php

namespace App\Controller\Admin;

use App\Entity\GalleryCategory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

final class GalleryCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string { return GalleryCategory::class; }
    public function configureCrud(Crud $crud): Crud { return $crud->setEntityLabelInSingular('Thématique')->setEntityLabelInPlural('Thématiques de galerie')->setDefaultSort(['displayOrder'=>'ASC']); }
    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab('Réglages');
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('slug','Identifiant')->setHelp('Exemple : preparation-naissance');
        yield IntegerField::new('displayOrder','Ordre');
        yield BooleanField::new('active','Publié');
        yield FormField::addTab('Français');
        yield TextField::new('titleFr','Titre'); yield TextareaField::new('descriptionFr','Introduction')->hideOnIndex();
        yield FormField::addTab('English');
        yield TextField::new('titleEn','Title')->hideOnIndex(); yield TextareaField::new('descriptionEn','Introduction')->hideOnIndex();
        yield FormField::addTab('العربية');
        yield TextField::new('titleAr','العنوان')->hideOnIndex()->setFormTypeOption('attr',['dir'=>'rtl']); yield TextareaField::new('descriptionAr','المقدمة')->hideOnIndex()->setFormTypeOption('attr',['dir'=>'rtl']);
    }
}
