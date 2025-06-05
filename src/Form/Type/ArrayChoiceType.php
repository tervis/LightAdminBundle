<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class ArrayChoiceType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(
            new CallbackTransformer(
                function ($itemsArray) {
                    return count($itemsArray) ? $itemsArray[0] : null; // transform the array to a string
                },
                function ($itemsString) {
                    return [$itemsString]; // transform the string back to an array
                }
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'expanded' => true,
            'multiple' => false,
        ]);
    }
}
