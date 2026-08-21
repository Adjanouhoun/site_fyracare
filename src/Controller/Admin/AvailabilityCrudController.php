<?php
namespace App\Controller\Admin;
use App\Entity\Availability;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
final class AvailabilityCrudController extends AbstractCrudController {
 public static function getEntityFqcn():string{return Availability::class;}
 public function configureCrud(Crud $c):Crud{return $c->setEntityLabelInSingular('Créneau')->setEntityLabelInPlural('Disponibilités')->setDefaultSort(['startsAt'=>'ASC']);}
 public function configureFields(string $p):iterable{yield IdField::new('id')->hideOnForm();yield DateTimeField::new('startsAt','Date et heure');yield BooleanField::new('active','Disponible');}
}
