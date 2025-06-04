<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\Dto;

use Tervis\LightAdminBundle\Contracts\MenuItemInterface;

final class MainMenu
{
    private array $items;

    /**
     * @param MenuItemInterface[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return MenuItemInterface[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
