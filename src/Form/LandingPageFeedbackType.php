<?php

namespace App\Form;

use App\Entity\LandingPageFeedback;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LandingPageFeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content',null,["label" => false,"attr" => ["placeholder" => "Digite aqui sua sugestao","class" => "form-input"]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LandingPageFeedback::class,
        ]);
    }
}
