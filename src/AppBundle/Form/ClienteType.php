<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClienteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('nombre')
                ->add('apellido')
                ->add('email')
                ->add('grupoCliente', 'choice', array(
                    'expanded' => true,
                    'multiple' => true,
                    'choices'  => array(
                        'Grupo_A' => 'Grupo A ',
                        'Grupo_B'  => 'Grupo B ',
                        'Grupo_C'   => 'Grupo C ',
                    ),
                    'choice_attr' => array('class' => 'form-check-input check'),
                ))
                ->add('observaciones')
                ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Cliente'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_cliente';
    }


}
