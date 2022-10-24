<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Categorie;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;

class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('nomArticle', TextType::class)
            // j'utilise un FileType , permet d'avoir un input pour uploader le fichier en question
            // resultat qui ne correspond pas au niveau de notre formulaire donc :
            // et donc ajout de champ unmapped ensuite et mettre à jour ce champ ensuite av getters et setters
            ->add('prixArticle', TextType::class)
            ->add('descriptionArticle', TextType::class)
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nomCategorie',
                'choice_value' => 'id',
            ])

            ->add('img', FileType::class, [
                'label' => 'image article',
                //// unmapped means that this field is not associated to any entity property
                // pas de champp associé au fichier de notre entity : (il n'y a pas de champ qui s'appelle img)
                'mapped' => false,
                //     // make it optional so you don't have to re-upload the PDF file
                //     // every time you edit the Product details
                'required' => false,
                //     // unmapped fields can't define their validation using annotations
                //     // in the associated entity, so you can use the PHP constraint classes
                // j'ajoute des contraintes :
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/jpg'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ])

            ->add('save', SubmitType::class, ['label' => 'Créer article']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class
        ]);
    }
}
