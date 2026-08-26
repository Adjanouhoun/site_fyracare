<?php

namespace App\Controller\Admin;

use App\Entity\GalleryItem;
use App\Service\ServiceImageResizer;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FileField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

final class GalleryItemCrudController extends AbstractCrudController
{
    public function __construct(private ServiceImageResizer $imageResizer) {}
    public static function getEntityFqcn(): string { return GalleryItem::class; }
    public function configureCrud(Crud $crud): Crud { return $crud->setEntityLabelInSingular('Média')->setEntityLabelInPlural('Galerie photo / vidéo')->setPageTitle(Crud::PAGE_INDEX,'Galerie photo / vidéo')->setDefaultSort(['displayOrder'=>'ASC']); }
    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab('Média');
        yield IdField::new('id')->hideOnForm();
        yield ChoiceField::new('type','Type')->setChoices(['Photo'=>GalleryItem::TYPE_IMAGE,'Vidéo'=>GalleryItem::TYPE_VIDEO]);
        yield IntegerField::new('displayOrder','Ordre');
        yield BooleanField::new('active','Publié');
        yield BooleanField::new('featured','Afficher sur l’accueil');
        yield FileField::new('mediaFile','Fichier photo ou vidéo')->setBasePath('/uploads/gallery')->setUploadDir('public/uploads/gallery')->setUploadedFileNamePattern('[slug]-[timestamp]-[randomhash].[extension]')->setFormTypeOptions(['required'=>false,'attr'=>['accept'=>'image/jpeg,image/png,image/webp,video/mp4,video/webm']])->setHelp('JPEG, PNG, WebP, MP4 ou WebM. Les grandes photos sont redimensionnées automatiquement.');
        yield TextField::new('videoUrl','Lien vidéo')->setHelp('YouTube, Vimeo ou URL directe vers une vidéo.')->hideOnIndex();
        yield ImageField::new('thumbnail','Aperçu vidéo')->setBasePath('/uploads/gallery')->setUploadDir('public/uploads/gallery')->setUploadedFileNamePattern('[slug]-thumb-[timestamp]-[randomhash].[extension]')->setFormTypeOptions(['required'=>false,'attr'=>['accept'=>'image/jpeg,image/png,image/webp']])->setHelp('Image facultative affichée avant la lecture.');
        yield FormField::addTab('Français');
        yield TextField::new('titleFr','Titre'); yield TextareaField::new('captionFr','Légende')->hideOnIndex();
        yield FormField::addTab('English');
        yield TextField::new('titleEn','Title')->hideOnIndex(); yield TextareaField::new('captionEn','Caption')->hideOnIndex();
        yield FormField::addTab('العربية');
        yield TextField::new('titleAr','العنوان')->hideOnIndex()->setFormTypeOption('attr',['dir'=>'rtl']); yield TextareaField::new('captionAr','الوصف')->hideOnIndex()->setFormTypeOption('attr',['dir'=>'rtl']);
    }
    public function persistEntity(EntityManagerInterface $em, $entity): void { parent::persistEntity($em,$entity); $this->resize($entity); }
    public function updateEntity(EntityManagerInterface $em, $entity): void { parent::updateEntity($em,$entity); $this->resize($entity); }
    private function resize(object $entity): void { if ($entity instanceof GalleryItem) { $this->imageResizer->resizeIn('gallery',$entity->getMediaFile()); $this->imageResizer->resizeIn('gallery',$entity->getThumbnail()); } }
}
