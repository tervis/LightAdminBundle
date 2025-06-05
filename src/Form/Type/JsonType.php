<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Handles json based input (for example multiselect tag)
 * Will only transform the json to array (and back),
 *
 * If necessary, could probably accept custom options (className) which would be used to
 * transform the data into given class via {@see SerializerInterface}
 */
class JsonType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function getParent(): string
    {
        return TextType::class;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(
            new CallbackTransformer(
                function ($value) {
                    if (empty($value)) {
                        return json_encode([]);
                    }

                    return json_encode($value);
                    // transform the array to a string
                },
                function ($value) {
                    if (empty($value)) {
                        return [];
                    }

                    return json_decode($value);
                    // reverse transform the string back to an array
                }
            )
        );
    }
}
