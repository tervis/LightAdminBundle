<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\Dto;

use Tervis\LightAdminBundle\Dto\MenuItem;

final class UserMenu
{
    /**
     * Should username be visible
     *
     * @var boolean
     */
    private bool $displayName = true;

    /**
     * User display name
     *
     * @var string|null
     */
    private ?string $name = null;

    /** @var MenuItem[] */
    private array $items;


    public function isNameDisplayed(): bool
    {
        return $this->displayName;
    }

    public function setDisplayName(bool $isDisplayed): static
    {
        $this->displayName = $isDisplayed;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return MenuItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * When configuring the application, you are passed an array of
     * MenuItemInterface objects; after building the user menu contents,
     * this method is called with MenuItem objects.
     *
     * @param MenuItemInterface[]|MenuItem[] $items
     */
    public function setItems(array $items): static
    {
        $this->items = $items;
        return $this;
    }
}
