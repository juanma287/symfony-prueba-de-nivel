<?php

namespace App\Form;

use App\Entity\Empresa;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


/**
 * Define el formulario utilizado para crear y manipular empresas.
 * @author Juan Manuel Lazzarini <juan.manuel.lazzarini@gmail.com>
 * 
 */
class EmpresaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, [
                'label'  => 'Nombre',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('telefono', null, [
                'label'  => 'Telefono',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('email', null, [
                'label'  => 'Email',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('sector', null, [
                'label'  => 'Sector',
                'attr' => [
                    'class' => 'form-control',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Empresa::class,
        ]);
    }
}
