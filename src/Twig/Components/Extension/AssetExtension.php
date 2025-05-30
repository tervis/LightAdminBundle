<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\AssetExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AssetExtension extends AbstractExtension
{


    public function getFunctions(): array
    {
        return [
            new TwigFunction('safe_asset', [AssetExtensionRuntime::class, 'safeAsset']),
        ];
    }
}
