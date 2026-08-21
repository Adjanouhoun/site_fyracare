<?php

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\Availability;
use App\Entity\Service;
use App\Repository\AvailabilityRepository;
use App\Repository\ServiceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AppointmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $locale = $options['locale'];
        $builder
            ->add('service', EntityType::class, ['class' => Service::class, 'choice_label' => fn(Service $s) => $s->getTitle($locale), 'query_builder' => fn(ServiceRepository $r) => $r->createQueryBuilder('s')->andWhere('s.active = true')->orderBy('s.displayOrder', 'ASC'), 'label' => 'booking.service'])
            ->add('availability', EntityType::class, ['class' => Availability::class, 'choice_label' => fn(Availability $a) => $a->getStartsAt()->format('d/m/Y · H:i'), 'query_builder' => fn(AvailabilityRepository $r) => $r->createQueryBuilder('a')->andWhere('a.active = true')->andWhere('a.startsAt > :now')->setParameter('now', new \DateTimeImmutable())->orderBy('a.startsAt', 'ASC'), 'placeholder' => 'booking.choose_slot', 'label' => 'booking.slot'])
            ->add('fullName', TextType::class, ['label' => 'booking.name'])
            ->add('phone', TelType::class, ['label' => 'booking.phone'])
            ->add('email', EmailType::class, ['required' => false, 'label' => 'booking.email'])
            ->add('note', TextareaType::class, ['required' => false, 'label' => 'booking.note'])
            ->add('website', TextType::class, ['mapped' => false, 'required' => false, 'attr' => ['tabindex' => '-1', 'autocomplete' => 'off'], 'row_attr' => ['class' => 'form-honeypot']]);
    }
    public function configureOptions(OptionsResolver $resolver): void { $resolver->setDefaults(['data_class' => Appointment::class, 'locale' => 'fr', 'translation_domain' => 'messages']); $resolver->setAllowedValues('locale', ['fr','en','ar']); }
}
