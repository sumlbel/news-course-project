<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username')
            ->add('email', EmailType::class);
        if ($options['doing'] == 'new') {
            $builder->add(
                'plainPassword',
                TextType::class,
                array(
                    'label' => 'Password'
                )
            );
        }
        $builder->add(
            'isActive', CheckboxType::class, array(
                'label'    => 'Is active',
                'required' => false)
        )
            ->add(
                'wantNewsletter', CheckboxType::class, array(
                'label'    => 'Receiving newsletter',
                'required' => false)
            )
            ->add(
                'roles', CollectionType::class, array(
                    'label' => 'Role',
                    'entry_type'   => ChoiceType::class,
                    'entry_options'  => array(
                        'label' => false,
                        'choices'  => array(
                            'User' => 'ROLE_USER',
                            'Moderator'     => 'ROLE_MODERATOR',
                            'Admin'    => 'ROLE_ADMIN'
                        )
                    )
                )
            );
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\User',
                'doing' => null
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_user';
    }


}
