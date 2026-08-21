<?php

namespace App\Controller\Admin;

use App\Entity\Testimonial;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\HttpFoundation\Response;

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
    public function configureActions(Actions $actions): Actions
    {
        $approve = Action::new('approve', 'Approuver', 'fa fa-check')->linkToCrudAction('approve')->displayIf(fn(Testimonial $t) => $t->getStatus() !== Testimonial::STATUS_APPROVED)->addCssClass('btn btn-success');
        $reject = Action::new('reject', 'Refuser', 'fa fa-xmark')->linkToCrudAction('reject')->displayIf(fn(Testimonial $t) => $t->getStatus() !== Testimonial::STATUS_REJECTED)->addCssClass('btn btn-danger');
        return $actions->add(Crud::PAGE_INDEX, $approve)->add(Crud::PAGE_INDEX, $reject);
    }
    public function approve(AdminContext $context, EntityManagerInterface $em): Response
    {
        $testimonial = $context->getEntity()->getInstance();
        if ($testimonial instanceof Testimonial) { $testimonial->setStatus(Testimonial::STATUS_APPROVED); $em->flush(); }
        return $this->redirect($context->getReferrer() ?: $this->generateUrl('admin_testimonial_index'));
    }
    public function reject(AdminContext $context, EntityManagerInterface $em): Response
    {
        $testimonial = $context->getEntity()->getInstance();
        if ($testimonial instanceof Testimonial) { $testimonial->setStatus(Testimonial::STATUS_REJECTED); $em->flush(); }
        return $this->redirect($context->getReferrer() ?: $this->generateUrl('admin_testimonial_index'));
    }
}
