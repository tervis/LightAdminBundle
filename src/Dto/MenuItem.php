<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\Dto;

use Tervis\LightAdminBundle\Contracts\MenuItemInterface;

class MenuItem implements MenuItemInterface
{
    public const TYPE_CRUD = 'crud';
    public const TYPE_URL = 'url';
    public const TYPE_SECTION = 'section';
    public const TYPE_EXIT_IMPERSONATION = 'exit_impersonation';
    public const TYPE_DASHBOARD = 'dashboard';
    public const TYPE_LOGOUT = 'logout';
    public const TYPE_SUBMENU = 'submenu';
    public const TYPE_ROUTE = 'route';

    private ?string $type = null;

    private ?string $label = null;

    private ?string $icon = null;

    private ?string $routeName = null;
    private array $routeParameters = [];

    /** @var MenuItem[] */
    private array $subItems = [];

    private ?string $permission = null;

    private ?string $linkUrl = null;

    public function __construct(?string $type = self::TYPE_ROUTE)
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set menu item type
     *
     * @param string $type
     * @return void
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getPermission(): ?string
    {
        return $this->permission;
    }

    /**
     * Undocumented function
     *
     * @param string|null $permission
     * @return static
     */
    public function setPermission(?string $permission = null): static
    {
        $this->permission = $permission;
        return $this;
    }

    /**
     * @return MenuItem[]
     */
    public function getSubItems(): array
    {
        return $this->subItems;
    }

    /**
     * @param MenuItem[] $subItems
     * @return self
     */
    public function setSubItems(array $subItems): static
    {
        $this->subItems = $subItems;
        return $this;
    }

    public function hasSubItems(): bool
    {
        return self::TYPE_SUBMENU === $this->type && \count($this->subItems) > 0;
    }

    /**
     * Get the value of label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the value of label
     *
     * @return  self
     */
    public function setLabel($label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the value of icon
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set the value of icon
     *
     * @return  self
     */
    public function setIcon($icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get the value of routeName
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * Set the value of routeName
     *
     * @return  self
     */
    public function setRouteName($routeName)
    {
        $this->routeName = $routeName;

        return $this;
    }

    /**
     * Get the value of routeParameters
     */
    public function getRouteParameters(): array
    {
        return $this->routeParameters;
    }

    /**
     * Set parameter key and value
     *
     * @return  self
     */
    public function setRouteParameter(string $key, string $value)
    {
        $this->routeParameters[$key] = $value;

        return $this;
    }

    /**
     * Set the value of routeParameters
     *
     * @return  self
     */
    public function setRouteParameters(array $routeParameters)
    {
        $this->routeParameters = $routeParameters;

        return $this;
    }

    public function getLinkUrl(): ?string
    {
        return $this->linkUrl;
    }

    public function setLinkUrl(?string $linkUrl): static
    {
        $this->linkUrl = $linkUrl;
        return $this;
    }
}
