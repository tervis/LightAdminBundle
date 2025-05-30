<?php

declare(strict_types=1);

namespace Tervis\Bundle\LightAdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class LightAdminBundleExtension extends Extension
{
    public function prepend(ContainerBuilder $builder): void
    {

        $builder->prependExtensionConfig('twig_component', [
            'defaults' => [
                'Tervis\\Bundle\\LightAdminBundle\\Twig\\Component\\' => [
                    'template_directory' => '@LightAdminBundle/components/',
                    'name_prefix' => 'la',
                ],
            ],
        ]);

        $bundleTemplatesOverrideDir = $builder->getParameter('kernel.project_dir') . '/templates/bundles/LightAdminBundle/';

        $builder->prependExtensionConfig('twig', [
            'paths' => is_dir($bundleTemplatesOverrideDir)
                ? [
                    'templates/bundles/LightAdminBundle/' => 'LightAdminBundle',
                    \dirname(__DIR__) . '/../templates/' => 'LightAdminBundle',
                ]
                : [
                    \dirname(__DIR__) . '/../templates/' => 'LightAdminBundle',
                ],
        ]);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Set parameters based on the processed configuration
        // $container->setParameter('tervis_lightadmin_bundle.my_option', $config['my_option']);
        // $container->setParameter('tervis_lightadmin_bundle.another_setting.enabled', $config['another_setting']['enabled']);
        // $container->setParameter('tervis_lightadmin_bundle.another_setting.limit', $config['another_setting']['limit']);

        // Load services from services.yaml
        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__) . '/Resources/config'));
        $loader->load('services.php');

        // You can also manipulate service definitions based on configuration
        // Example: if a specific option is enabled, add a tag to a service
        // if ($config['another_setting']['enabled']) {
        //     $definition = $container->getDefinition('MyVendor\MyBundle\Service\MyService');
        //     $definition->addTag('lightadmin_bundle.feature_enabled');
        // }
    }

    public function getAlias(): string
    {
        return 'light_admin';
    }
}
