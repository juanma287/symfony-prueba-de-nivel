<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

/**
 * Define el formulario utilizado para filtrar empresas.
 * @author Juan Manuel Lazzarini <juan.manuel.lazzarini@gmail.com>
 * 
 */
class EmpresaFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', Filters\TextFilterType::class, [
                'label'  => 'Nombre',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('sector', Filters\EntityFilterType::class, [
                'placeholder' => 'Seleccionar...',
                'class' => 'App\Entity\Sector',
                'choice_label' => 'nombre',
                'label' => 'Sector', 
                'attr' => [
                    'class' => 'form-control',
                ]                       
            ]);
        $builder->setMethod("GET");    
    }



    public function getBlockPrefix()
    {
        return 'item_filter';
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => true,
            'csrf_protection' => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}
