<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\Options;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Tervis\LightAdminBundle\Form\Type\ArrayChoiceType;
use Tervis\LightAdminBundle\Form\Type\JsonType;

enum FieldType: string
{
    case Text = 'text';
    case Textarea = 'textarea';
    case Date = 'date';
    case DateTime = 'datetime';
    case Boolean = 'boolean';
    case Email = 'email';
    case Number = 'number';
    case Money = 'money';
    case Json = 'json';
    case Choice = 'choice';
    case Array = 'array';

    // Add more cases as needed, ensuring the string value matches your input strings

    /**
     * Maps the FieldType enum case to its corresponding Symfony Form Type class.
     *
     * @return string The fully qualified class name of the Symfony Form Type.
     */
    public function toSymfonyFormType(): string
    {
        return match ($this) {
            self::Text => TextType::class,
            self::Textarea => TextareaType::class,
            self::Date => DateType::class,
            self::DateTime => DateTimeType::class,
            self::Boolean => CheckboxType::class,
            self::Email => EmailType::class,
            self::Number => NumberType::class,
            self::Money => MoneyType::class,
            self::Choice => ChoiceType::class,
            self::Json => JsonType::class,
            self::Array => ArrayChoiceType::class,
            // If you add new cases, make sure to add them here as well.
        };
    }

    /**
     * Creates a FieldType enum instance from a string value.
     * Defaults to FieldType::Text if the string value is not found.
     *
     * @param string|null $value The string representation of the field type.
     * @return self
     */
    public static function fromString(?string $value): self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }
        // Default behavior if the string is not recognized
        return self::Text;
    }
}
