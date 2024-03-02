<?php

namespace App\Form;

use App\Entity\LandingPageLead;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LandingPageLeadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, ["label" => "Nome completo"])
            ->add('email', EmailType::class)
            ->add('phone', null, ["label" => "Numero de telefone", "attr" => ["data-inputmask" => "'mask': '(99) 99999-9999'"]])
            ->add('selectedPlan', ChoiceType::class, ["label" => "Plano selecionado",
                "choices" => [
                    "1 Usuario" => "1_user",
                    "5 Usuarios" => "5_users",
                    "25 Usuarios" => "25_users",
                    "Sob medida" => "on_demand",
                ]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LandingPageLead::class,
        ]);
    }
}
