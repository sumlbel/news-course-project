<?php

namespace AppBundle\Form;

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
        $builder->add('title', TextType::class);
        $builder->add('author', TextType::class);
        $builder->add(
            'category', EntityType::class, array(
                'class' => 'AppBundle:Category',
                'choice_label' => 'name',
                'placeholder' => 'Choose category',
            )
        );
        $builder->add(
            'publicationDate', DateType::class, array(
                'format' => 'dd MMMM yyyy',
                'data' => new \DateTime(),
                )
        );
        $builder->add('description', TextareaType::class);
        $builder->add('body', TextareaType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\Article',
            )
        );
    }

    public function getBlockPrefix()
    {
        return 'news_form';
    }
}
