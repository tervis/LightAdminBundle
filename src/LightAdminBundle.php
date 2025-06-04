<?php

declare(strict_types=1);

namespace Tervis\LightAdminBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ConfigurableExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;





class LightAdminBundle extends AbstractBundle
{
    public const VERSION = '0.0.1';

    protected string $extensionAlias = 'light_admin';

    public function configure(DefinitionConfigurator $definition): void
    {
        // loads config definition from a file
        //$definition->import('../config/definition.php');

        // loads config definition from multiple files (when it's too long you can split it)
        //$definition->import('../config/definition/*.php');

        // if the configuration is short, consider adding it in this class
        $definition->rootNode()
            ->children()
            ->scalarNode('foo')->defaultValue('bar')->end()
            ->end()
        ;
    }

    // $config is the bundle Configuration that you usually process in
    // ExtensionInterface::load() but already merged and processed
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        //$container->parameters()->set('foo', $config['foo']);
        $container->import('../config/services.php');

        // if ('bar' === $config['foo']) {
        //     $container->services()->set(Parser::class);
        // }
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        // prepend some config option
        // $builder->prependExtensionConfig('framework', [
        //     'cache' => ['prefix_seed' => 'foo/bar'],
        // ]);

        // append some config option
        $container->extension('framework', [
            //'cache' => ['prefix_seed' => 'foo/bar'],
            'assets' => [
                'packages' => [
                    'lightadmin' => [
                        'base_path' => 'bundles/lightadmin',
                    ],
                ],
            ]
        ]);

        $bundleTemplatesOverrideDir = $builder->getParameter('kernel.project_dir') . '/templates/bundles/LightAdminBundle/';

        $builder->prependExtensionConfig('twig', [
            'paths' => is_dir($bundleTemplatesOverrideDir)
                ? [
                    'templates/bundles/LightAdminBundle/' => 'LightAdmin',
                    \dirname(__DIR__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR => 'LightAdmin',
                ]
                : [
                    \dirname(__DIR__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR => 'LightAdmin',
                ],
        ]);
    }
}
