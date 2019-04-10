<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', FileType::class, [
            'label' => 'Pick your picture',
            'required' => false,
            'label_attr' => [
                'class' => 'control-label',
            ],
            'attr' => [
                'class' => 'form-control',
                'accept' => 'image/jpeg,image/jpg,image/png,image/gif',
            ],
        ])->add(
            'isActive', CheckboxType::class, [
                'label' => 'Active',
                'data' => true,
                'required' => false,
                'label_attr' => [
                    'class' => 'control-label',
                ],
            ])->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Save',
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Image',
        ]);
    }
}
