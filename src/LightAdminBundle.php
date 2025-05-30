<?php

declare(strict_types=1);

namespace Tervis\Bundle\LightAdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class LightAdminBundle extends Bundle
{
    public const VERSION = '0.0.1';

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
