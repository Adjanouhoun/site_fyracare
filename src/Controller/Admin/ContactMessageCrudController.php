<?php
namespace App\Controller\Admin;
use App\Entity\ContactMessage;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
final class ContactMessageCrudController extends AbstractCrudController {
 public static function getEntityFqcn():string{return ContactMessage::class;}
 public function configureCrud(Crud $c):Crud{return $c->setEntityLabelInSingular('Message')->setEntityLabelInPlural('Messages')->setDefaultSort(['createdAt'=>'DESC']);}
 public function configureFields(string $p):iterable{yield IdField::new('id')->hideOnForm();yield TextField::new('name','Nom');yield TextField::new('email','E-mail');yield TextField::new('phone','Téléphone');yield TextField::new('subject','Sujet');yield TextareaField::new('message','Message')->hideOnIndex();yield ChoiceField::new('status','Statut')->setChoices(['Nouveau'=>ContactMessage::STATUS_NEW,'Traité'=>ContactMessage::STATUS_PROCESSED])->renderAsBadges([ContactMessage::STATUS_NEW=>'warning',ContactMessage::STATUS_PROCESSED=>'success']);yield DateTimeField::new('createdAt','Reçu le')->hideOnForm();}
}
