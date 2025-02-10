<?php

namespace App\Form;

use App\Entity\Livre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class LivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                "label" => "Titre du livre",
                "constraints" => [
                    new Length([
                        "min" => 4,
                        "minMessage" => "Le titre doit avoir au moins 4 caractères",
                        "max" => 50,
                        "maxMessage" => "Le titre doit avoir au maximum 50 caractères"
                    ]),
                    new NotBlank([
                        "message" => "Le titre ne peut être vide"
                    ])
                ]
            ])
            ->add('auteur', TextType::class, [
                "label" => "Nom de l'auteur",
            ])

            ->add('enregistrer', SubmitType::class, [
                "label" => $options["edit_mode"] ? "Modifier" : "Enregistrer",
                "attr" => [
                    "class" => "btn btn-success",
                    
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
            "edit_mode" => false

        ]);
    }
}
