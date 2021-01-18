<?php

namespace App\Form;

use App\Entity\Livre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Image;

class LivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, [
               "constraints" => [
                   new Length([
                       "max" => 100,
                       "maxMessage" => "Le titre ne peut comporter que {{ limit }} caractères"
                   ]),
                   new NotBlank([ "message" => "Le titre ne peut pas être vide" ])
               ] 
            ])
            ->add('auteur', TextType::class, [
                "constraints" => [
                    new Length([
                        "max" => 50,
                        "maxMessage" => "L'auteur ne peut comporter que {{ limit }} caractères"
                    ]),
                    new NotBlank([ "message" => "L'auteur ne peut pas être vide" ])
                ] 
             ])
            ->add('couverture', FileType::class, [
                "mapped" => false,
                "required" => false,
                "constraints" => [
                    new Image([
                        "mimeTypes" => ["image/png", "image/jpeg", "image/gif"],
                        "mimeTypesMessage" => "Vous ne pouvez téléchargé que des fichiers jpg, png ou gif",
                        "maxSize" => "2048k",
                        "maxSizeMessage" => "Le fichier ne doit pas dépasser 2Mo"
                    ])
                ]
            ])
            ->add("enregistrer", SubmitType::class, [
                "attr" => [ 
                    "class" => "btn btn-primary"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}
