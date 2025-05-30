<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\Dto;

use Tervis\LightAdminBundle\Options\PageAction;

class Crud
{
    public string $dateTimePattern = 'Y-m-d H:i:s';

    public string $datePattern = 'Y-m-d';

    private ?string $entityName = null;

    private ?string $pagePreTitle = null;
    private ?string $pageTitle = null;

    private bool $actionIndex = true;
    private bool $actionDetails = true;
    private bool $actionEdit = true;
    private bool $actionNew = true;
    private bool $actionDelete = true;
    private bool $actionBatchDelete = true;

    /**
     * Entity fields
     *
     * @var array
     */
    private array $collection = [];

    /**
     * Page actions
     *
     * @var Action[]
     */
    private array $actions = [];

    /**
     * List view filters
     *
     * @var Filter[]
     */
    private array $filters = [];

    /**
     * Get undocumented variable
     *
     * @return  Action[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    public function getAction(string $action): ?Action
    {
        if (in_array($action, array_keys($this->actions))) {
            return $this->actions[$action];
        }

        return null;
    }

    /**
     * Set undocumented variable
     *
     * @param  Action[]  $actions  Undocumented variable
     *
     * @return  self
     */
    public function setActions(array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Get the value of entityName
     */
    public function getEntityName(): string
    {
        return $this->entityName;
    }

    /**
     * Set the value of entityName
     *
     * @return  self
     */
    public function setEntityName($entityName): static
    {
        $this->entityName = $entityName;

        return $this;
    }

    /**
     * Get entity fields
     *
     * @return  array
     */
    public function getCollection(): array
    {
        return $this->collection;
    }

    /**
     * Set entity fields
     *
     * @param  array  $collection  Entity fields
     *
     * @return  self
     */
    public function setCollection(array $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get the value of pagePreTitle
     */
    public function getPagePreTitle(): string
    {
        return $this->pagePreTitle ?: 'Management';
    }

    /**
     * Set the value of pagePreTitle
     *
     * @return  self
     */
    public function setPagePreTitle(string $pagePreTitle): static
    {
        $this->pagePreTitle = $pagePreTitle;

        return $this;
    }

    /**
     * Get the value of pageTitle
     */
    public function getPageTitle(): string
    {
        return $this->pageTitle ?: $this->entityName;
    }

    /**
     * Set the value of pageTitle
     *
     * @return  self
     */
    public function setPageTitle($pageTitle): static
    {
        $this->pageTitle = $pageTitle;

        return $this;
    }

    public function disableAction(string $action): static
    {
        try {
            $action = PageAction::from($action);
            match ($action) {
                PageAction::List => $this->actionIndex = false,
                PageAction::New => $this->actionNew = false,
                PageAction::Details => $this->actionDetails = false,
                PageAction::Edit => $this->actionEdit = false,
                PageAction::Delete => $this->actionDelete = false,
                PageAction::BatchDelete => $this->actionBatchDelete = false
            };
        } catch (\Throwable $e) {
        }

        return $this;
    }

    public function enableAction(string $action): static
    {
        try {
            $action = PageAction::from($action);
            match ($action) {
                PageAction::List => $this->actionIndex = true,
                PageAction::New => $this->actionNew = true,
                PageAction::Details => $this->actionDetails = true,
                PageAction::Edit => $this->actionEdit = true,
                PageAction::Delete => $this->actionDelete = true,
                PageAction::BatchDelete => $this->actionBatchDelete = true
            };
        } catch (\Throwable $e) {
        }

        return $this;
    }

    public function isActionEnabled(string $action): bool
    {
        try {
            $action = PageAction::from($action);
            return match ($action) {
                PageAction::List => $this->actionIndex,
                PageAction::New => $this->actionNew,
                PageAction::Details => $this->actionDetails,
                PageAction::Edit => $this->actionEdit,
                PageAction::Delete => $this->actionDelete,
                PageAction::BatchDelete => $this->actionBatchDelete,
                default => false
            };
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get the value of filters
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Set the value of filters
     *
     * @return  self
     */
    public function setFilters($filters): static
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Get page fields
     *
     * @param string $action
     * @return array
     */
    public function getFields(string $action): array
    {
        $fields = [];
        $page = $this->getAction($action);

        if ($page) {
            $fields = $page->getFields();
        }

        if (empty($fields)) {
            return [];
        }

        return array_filter($fields, function ($item) use ($action) {
            if (empty($item->showOnPages)) {
                return true;
            }
            return $item->hasPage($action);
        });
    }
}
