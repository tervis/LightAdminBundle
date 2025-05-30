<?php

declare(strict_types=1);

namespace Tervis\Bundle\LightAdminBundle\Field;

use Tervis\Bundle\LightAdminBundle\Contracts\FieldInterface;
use Tervis\Bundle\LightAdminBundle\Options\FieldType;
use Tervis\Bundle\LightAdminBundle\Options\PageAction;

class Field implements FieldInterface
{
    public string $propertyName;
    public ?string $label = null;
    public array $showOnPages = []; // e.g., ['list', 'detail', 'form']
    public bool $isSortable = true; // For list view
    public bool $isFilterable = false; // For list view
    public bool $isDisabled = false;
    public ?string $fieldType = null; // e.g., 'text', 'textarea', 'date', 'boolean', 'choice'
    public array $formOptions = []; // Symfony Form options: 'attr', 'required', 'choices', etc.
    public ?string $displayFormat = null; // For list/detail views: 'date_short', 'currency', 'boolean_icon'
    public ?string $template = null; // Custom Twig template for rendering this field

    /**
     * For switch field and set only in asSwitch method
     *
     * @var string|null
     */
    private ?string $switchPath = null;

    private function __construct(string $propertyName)
    {
        $this->propertyName = $propertyName;
        $this->label = ucfirst(preg_replace('/(?<!^)[A-Z]/', ' $0', $propertyName)); // Default label from camelCase
    }

    public static function new(string $propertyName, ?string $label = null): Field
    {
        $field = (new self($propertyName))->setType(FieldType::Text->value);
        if ($label !== null) {
            $field->setLabel($label);
        }
        return $field;
    }

    /**
     * Get the value of propertyName
     */
    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    /**
     * Set the value of propertyName
     *
     * @return  self
     */
    public function setPropertyName(string $propertyName): static
    {
        $this->propertyName = $propertyName;

        return $this;
    }

    /**
     * Get the value of label
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Set the value of label
     *
     * @return  self
     */
    public function setLabel(?string $label = null): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the value of type
     */
    public function getType(): string
    {
        return $this->fieldType;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */
    public function setType(string $type): static
    {
        $this->fieldType = $type;

        return $this;
    }

    /**
     * Pages field is used
     *
     * @param string ...$actions
     * @return self
     */
    public function showOn(string ...$pages): static
    {
        $arr = array_unique($this->showOnPages);

        foreach ($pages as $page) {
            $arr[] = $page;
        }

        $this->showOnPages = array_unique($arr);

        return $this;
    }

    /**
     * Hide field on forms
     *
     * @return self
     */
    public function hideOnForm(): static
    {
        $this->showOnPages = array_values(array_filter($this->showOnPages, fn($page) => $page !== 'form'));
        if (empty($this->showOnPages)) { // If nothing else set, show on list and detail by default
            $this->showOnPages = [PageAction::List->value, PageAction::Details->value];
        }
        return $this;
    }

    /**
     * Show only on forms
     *
     * @return static
     */
    public function onlyOnForms(): static
    {
        $this->showOnPages = [PageAction::Edit->value, PageAction::New->value];
        return $this;
    }

    // New methods for field type and rendering
    public function asJson(): self
    {
        $this->fieldType = FieldType::Json->value;
        return $this;
    }

    public function asText(): self
    {
        $this->fieldType = FieldType::Text->value;
        return $this;
    }

    public function asTextarea(): self
    {
        $this->fieldType = FieldType::Textarea->value;
        return $this;
    }

    public function asDate(?string $format = null): self
    {
        $this->fieldType = FieldType::Date->value;
        $this->displayFormat = $format ?? 'Y-m-d';
        return $this;
    }

    public function asDateTime(?string $format = null): self
    {
        $this->fieldType = FieldType::DateTime->value;
        $this->displayFormat = $format ?? 'Y-m-d H:i:s';
        return $this;
    }

    public function asBoolean(): self
    {
        $this->fieldType = FieldType::Boolean->value;
        return $this;
    }

    public function asSwitch(string $path): self
    {
        $this->fieldType = FieldType::Boolean->value;
        $this->switchPath = $path;
        return $this;
    }

    public function asEmail(): self
    {
        $this->fieldType = FieldType::Email->value;
        return $this;
    }

    public function asNumber(): self
    {
        $this->fieldType = FieldType::Number->value;
        return $this;
    }

    public function asMoney(string $currency = 'EUR'): self
    {
        $this->fieldType = FieldType::Money->value;
        $this->displayFormat = $currency;
        return $this;
    }

    public function addFormOption(string $key, $value): self
    {
        $this->formOptions[$key] = $value;
        return $this;
    }

    public function setFormOptions(array $options): self
    {
        $this->formOptions = $options;
        return $this;
    }

    public function setDisplayFormat(string $format): self
    {
        $this->displayFormat = $format;
        return $this;
    }

    public function useTemplate(string $templatePath): self
    {
        $this->template = $templatePath;
        return $this;
    }

    /**
     * Helper for checking if field should be shown on a page
     *
     * @param string $page
     * @return boolean
     */
    public function hasPage(string $page): bool
    {
        return in_array($page, $this->showOnPages);
    }

    /**
     * Get the value of switchPath
     */
    public function getSwitchPath(): ?string
    {
        return $this->switchPath;
    }
}
