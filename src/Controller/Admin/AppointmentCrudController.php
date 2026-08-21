<?php
namespace App\Controller\Admin;
use App\Entity\Appointment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
final class AppointmentCrudController extends AbstractCrudController {
 public static function getEntityFqcn():string{return Appointment::class;}
 public function configureCrud(Crud $c):Crud{return $c->setEntityLabelInSingular('Rendez-vous')->setEntityLabelInPlural('Rendez-vous')->setDefaultSort(['createdAt'=>'DESC']);}
 public function configureFields(string $p):iterable{yield IdField::new('id')->hideOnForm();yield AssociationField::new('service','Prestation');yield AssociationField::new('availability','Créneau');yield TextField::new('fullName','Cliente');yield TextField::new('phone','Téléphone');yield TextField::new('email','E-mail')->hideOnIndex();yield TextareaField::new('note','Note')->hideOnIndex();yield ChoiceField::new('status','Statut')->setChoices(['En attente'=>Appointment::STATUS_PENDING,'Confirmé'=>Appointment::STATUS_CONFIRMED,'Réalisé'=>Appointment::STATUS_COMPLETED,'Annulé'=>Appointment::STATUS_CANCELLED])->renderAsBadges([Appointment::STATUS_PENDING=>'warning',Appointment::STATUS_CONFIRMED=>'success',Appointment::STATUS_COMPLETED=>'info',Appointment::STATUS_CANCELLED=>'danger']);yield DateTimeField::new('createdAt','Demandé le')->hideOnForm();}
}
