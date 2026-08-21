<?php

namespace App\Form;

use App\Entity\Testimonial;
use App\Repository\ServiceRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TestimonialType extends AbstractType
{
    public function __construct(private ServiceRepository $services) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $locale = $options['locale'];
        $careChoices = [];
        foreach ($this->services->findActive() as $service) $careChoices[$service->getTitle($locale)] = $service->getTitle($locale);
        $builder
            ->add('author', TextType::class, ['label' => 'testimonials.form_name'])
            ->add('care', ChoiceType::class, ['label' => 'testimonials.form_care', 'choices' => $careChoices, 'placeholder' => 'testimonials.form_choose'])
            ->add('rating', ChoiceType::class, ['label' => 'testimonials.form_rating', 'choices' => ['★★★★★' => 5, '★★★★☆' => 4, '★★★☆☆' => 3, '★★☆☆☆' => 2, '★☆☆☆☆' => 1]])
            ->add('content', TextareaType::class, ['label' => 'testimonials.form_message', 'attr' => ['rows' => 5]]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Testimonial::class, 'locale' => 'fr']);
        $resolver->setAllowedTypes('locale', 'string');
    }
}
