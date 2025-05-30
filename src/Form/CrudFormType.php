<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\Form;

use Tervis\LightAdminBundle\Field\Field;
use Tervis\LightAdminBundle\Options\FieldType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CrudFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Field[] $crudFields */
        $crudFields = $options['crud_fields'];

        foreach ($crudFields as $field) {
            $symfonyFormType = $this->mapFieldTypeToSymfonyFormType($field->fieldType);

            $builder->add($field->propertyName, $symfonyFormType, array_merge([
                'label' => $field->label,
            ], $field->formOptions));
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => null, // Data class will be set dynamically by the controller
            'crud_fields' => [], // Expect an array of Field objects
        ]);

        $resolver->setRequired('crud_fields');
        $resolver->setAllowedTypes('crud_fields', 'array');
    }

    /**
     * Maps a string field type to its corresponding Symfony Form Type class using an Enum.
     *
     * @param string|null $fieldType The string representation of the field type.
     * @return string The fully qualified class name of the Symfony Form Type.
     */
    protected function mapFieldTypeToSymfonyFormType(?string $fieldType): string
    {
        // Convert the input string to the FieldType Enum case
        $enumFieldType = FieldType::fromString($fieldType);

        // Call the method on the Enum case to get the Symfony Form Type
        return $enumFieldType->toSymfonyFormType();
    }
}
