<?php

namespace AppBundle\Form;

use KMS\FroalaEditorBundle\Form\Type\FroalaEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('author', TextType::class)
            ->add(
                'category', EntityType::class, array(
                    'class' => 'AppBundle:Category',
                    'choice_label' => 'name',
                    'placeholder' => 'Choose category',
                    )
            )
            ->add(
                'publicationDate', DateType::class, array(
                    'format' => 'dd MMMM yyyy',
                    )
            )
            ->add('description', TextareaType::class)
            ->add(
                'body', FroalaEditorType::class, array("language" => "en_gb")
            )
            ->add(
                'similarArticles'
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\Article',
                'isNew' => true
            )
        );
    }
}
