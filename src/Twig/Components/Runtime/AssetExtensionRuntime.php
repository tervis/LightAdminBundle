<?php

declare(strict_types=1);

namespace App\Twig\Runtime;

use Symfony\Component\Asset\Packages;
use Twig\Extension\RuntimeExtensionInterface;

class AssetExtensionRuntime implements RuntimeExtensionInterface
{
    private $packages;
    private $projectDir;

    public function __construct(Packages $packages, string $projectDir)
    {
        $this->packages = $packages;
        $this->projectDir = $projectDir;
    }

    public function safeAsset(string $path, string $defaultPath): string
    {
        $realPath = sprintf('%s/assets/%s', $this->projectDir, $path);

        if (file_exists($realPath)) {
            return $this->packages->getUrl($path);
        }

        return $this->packages->getUrl($defaultPath);
    }
}
