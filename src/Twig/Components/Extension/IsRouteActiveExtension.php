<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Twig\Runtime\IsRouteActiveExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IsRouteActiveExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_active_route', [IsRouteActiveExtensionRuntime::class, 'isActiveRoute']),
            new TwigFunction('is_active_route_prefix', [IsRouteActiveExtensionRuntime::class, 'isActiveRoutePrefix']),
        ];
    }
}
