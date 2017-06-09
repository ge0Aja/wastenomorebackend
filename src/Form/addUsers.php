<?php
/**
 * Created by PhpStorm.
 * User: saugo
 * Date: 9/2/16
 * Time: 12:50 PM
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class addUsers extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',TextType::class)
            ->add('fname', TextType::class)
            ->add('lname', TextType::class)
            ->add('role',ChoiceType::class,['choices'=>[
                'ROLE_Admin' => 'ROLE_Admin',
                'ROLE_Professor' => 'ROLE_Professor',
                'ROLE_Coordinator' => 'ROLE_Coordinator',
                'ROLE_Assistant' => 'ROLE_Assistant',

            ]])
        ->add('password',RepeatedType::class, array(
            'type' => PasswordType::class,
            'first_options'  => array('label' => 'Password'),
            'second_options' => array('label' => 'Repeat Password'),
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Users',
        ));
    }
}