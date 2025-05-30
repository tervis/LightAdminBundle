<?php

declare(strict_types=1);

namespace LightAdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('light_admin'); // 'light_admin' on konfiguraation pääavain
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
