<?php

namespace App\Form;

use App\Entity\Credentials;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CredentialsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextareaType::class, ["required" => false,])
            ->add('type', ChoiceType::class, [
                "choices" => [
                    "Api Key" => "key-auth",
                    "Basic Auth" => "basic-auth",
                    "Oauth 2" => "oauth2",
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Credentials::class,
        ]);
    }
}
