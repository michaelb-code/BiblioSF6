<?php

namespace App\Form;

use App\Entity\Abonne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Regex;

class AbonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $abonne = $options['data'];

        $builder
            ->add('pseudo')

            ->add('roles',ChoiceType::class, [
                "choices"=> [
                    "Directeur" => "ROLE_ADMIN",
                    "Bibliothécaire" => "ROLE_BIBLIO",
                    "Lecteur" => "ROLE_LECTEUR"
                ],
                "multiple" => true,
                "expanded" => true
            ])

            ->add('password' ,PasswordType::class, [
                "mapped" => false,
                "label" => "Mot de passe",
                "required" => $abonne->getId() ? false : true,
            // "constraints" => [
                //     new Regex([
                //         // Ici un pattern forçant une min, une maj, un chiffre, un caractère spécial, entre 6 et 10 carac
                //         "pattern" => "/^(?=.*[A-Z])(?=.*[a-z])(?=.*[\d])(?=.*[-+!*$@%_])([-+!*$@%_\w]{6,10})$/",
                //         "message" => "Attention votre mdp ne correspond pas au pattern, au moins 1 min 1 maj 1 chiffre 1 carac spécial et faire entre 6 et 10"
                //     ])
                // ]
            ])

            ->add('prenom')
            ->add('nom')
        ; 
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Abonne::class,
            "required_password" => false,
        ]);
    }
}
