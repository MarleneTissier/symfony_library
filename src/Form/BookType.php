<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Genre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('nbPages')
            ->add('genre', EntityType::class, [
                'class'=> Genre::class,
                'choice_label'=>'name'
            ])
            ->add('author', EntityType::class, [
                'class'=> Author::class,
                'choice_label'=>'lastname'
            ])
            // créer l'input File, avec en option "mapped => false" pour
            // que symfony n'enregistre pas automatiquement la valeur du champs
            ->add('bookCover', FileType::class, [
                'mapped' => false
            ])
            ->add('resume')
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
