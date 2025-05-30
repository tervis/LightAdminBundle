<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class LightAdminBundleExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $builder): void
    {
        $builder->prependExtensionConfig('twig_component', [
            'defaults' => [
                'Tervis\\Bundle\\LightAdminBundle\\Twig\\Component\\' => [
                    'template_directory' => '@LightAdmin/components/',
                    'name_prefix' => 'la',
                ],
            ],
        ]);

        $bundleTemplatesOverrideDir = $builder->getParameter('kernel.project_dir') . '/templates/bundles/LightAdminBundle/';

        $builder->prependExtensionConfig('twig', [
            'paths' => is_dir($bundleTemplatesOverrideDir)
                ? [
                    'templates/bundles/LightAdminBundle/' => 'LightAdmin',
                    \dirname(__DIR__) . '/../templates/' => 'LightAdmin',
                ]
                : [
                    \dirname(__DIR__) . '/../templates/' => 'LightAdmin',
                ],
        ]);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        // Ladataan myöhemmin palvelutiedostot, reitit jne.
        $loader->load('services.yaml');
        // $loader->load('routes.yaml');
    }

    public function getAlias(): string
    {
        return 'light_admin';
    }
}
