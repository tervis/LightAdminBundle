<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\Contracts;

interface FieldInterface
{
    /**
     * Create new Field object
     *
     * @param string $propertyName
     * @param string|null $label
     * @return void
     */
    public static function new(string $propertyName, ?string $label = null);
}
