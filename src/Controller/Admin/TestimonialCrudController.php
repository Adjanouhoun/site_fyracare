<?php

namespace App\Controller\Admin;

use App\Entity\Testimonial;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

final class TestimonialCrudController extends \EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController
{
    public static function getEntityFqcn(): string { return Testimonial::class; }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInSingular('Avis')->setEntityLabelInPlural('Avis clients')->setPageTitle(Crud::PAGE_INDEX, 'Modération des avis')->setDefaultSort(['createdAt' => 'DESC']);
    }
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('author', 'Cliente');
        yield TextField::new('care', 'Prestation');
        yield IntegerField::new('rating', 'Note');
        yield TextareaField::new('content', 'Avis')->hideOnIndex();
        yield ChoiceField::new('status', 'Statut')->setChoices(['En attente' => Testimonial::STATUS_PENDING, 'Publié' => Testimonial::STATUS_APPROVED, 'Refusé' => Testimonial::STATUS_REJECTED])->renderAsBadges([Testimonial::STATUS_PENDING => 'warning', Testimonial::STATUS_APPROVED => 'success', Testimonial::STATUS_REJECTED => 'danger']);
        yield DateTimeField::new('createdAt', 'Déposé le')->hideOnForm();
    }
}
