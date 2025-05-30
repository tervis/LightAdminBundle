<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\Dto;

class Pagination
{
    public int $page = 0;
    public int $totalPages = 0;
    public int $itemsPerPage = 0;
    public int $totalItems = 0;

    public function __construct(int $page, int $totalPages, int $itemsPerPage, int $totalItems)
    {
        $this->page = $page;
        $this->totalPages = $totalPages;
        $this->itemsPerPage = $itemsPerPage;
        $this->totalItems = $totalItems;
    }
}
