<?php
namespace App\Form;
use App\Entity\ContactMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
final class ContactMessageType extends AbstractType {
 public function buildForm(FormBuilderInterface $b,array $o):void{$b->add('name',TextType::class,['label'=>'contact.name'])->add('email',EmailType::class,['label'=>'contact.email'])->add('phone',TelType::class,['required'=>false,'label'=>'contact.phone'])->add('subject',TextType::class,['label'=>'contact.subject'])->add('message',TextareaType::class,['label'=>'contact.message','attr'=>['rows'=>7]])->add('website',TextType::class,['mapped'=>false,'required'=>false,'attr'=>['tabindex'=>'-1','autocomplete'=>'off'],'row_attr'=>['class'=>'form-honeypot']]);}
 public function configureOptions(OptionsResolver $r):void{$r->setDefaults(['data_class'=>ContactMessage::class,'translation_domain'=>'messages']);}
}
