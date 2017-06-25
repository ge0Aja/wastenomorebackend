<?php

namespace AppBundle\Form;

use AppBundle\Form\Model\ChangePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('password', PasswordType::class, array(
            'label' => 'Old password'
        ));
        $builder->add('plainPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'invalid_message' => 'The password fields must match.',
            'required' => true,
            'first_options'  => array('label' => 'New Password'),
            'second_options' => array('label' => 'Repeat Password'),
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
       /* $resolver->setDefaults(array(
        'data_class' => ChangePassword::class,
    ));*/

    }

    public function getBlockPrefix()
    {
        return 'app_bundle_change_password_type';
    }
}
