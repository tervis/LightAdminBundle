<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Twig\Runtime\LocaleExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LocaleExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('locales', [LocaleExtensionRuntime::class, 'getLocales']),
            new TwigFunction('localeName', [LocaleExtensionRuntime::class, 'getLocaleName']),
        ];
    }
}
