<?php

declare(strict_types=1);

namespace Tervis\Bundle\LightAdminBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class LightAdminBundle extends AbstractBundle
{
    public const VERSION = '0.0.1';

    public function getPath(): string
    {
        $reflected = new \ReflectionObject($this);

        return \dirname($reflected->getFileName(), 2);
    }
}
