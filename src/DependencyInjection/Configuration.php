<?php

declare(strict_types=1);

namespace Tervis\Bundle\LightAdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('light_admin');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->arrayNode('twig')
            ->children()
            ->arrayNode('paths')
            ->addDefaultChildrenIfNoneSet()
            ->prototype('scalar')->defaultValue(['%kernel.project_dir%/templates/LightAdmin' => 'lightAdmin'])->end()
            //'%kernel.project_dir%/templates/LightAdmin': lightAdmin
            ->end()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
