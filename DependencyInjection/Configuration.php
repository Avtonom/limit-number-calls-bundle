<?php

/**
 * @author Anton U <avtonomspb@gmail.com>
 */
namespace Avtonom\LimitNumberCallsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('avtonom_limit_number_calls');

        $this->addClassSection($rootNode);

        return $treeBuilder;
    }

    /**
     *
     * @access private
     * @param ArrayNodeDefinition $node
     *
     * http://symfony.com/doc/2.1/components/config/definition.html#normalization
     */
    private function addClassSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('rules')
                    ->isRequired()
                    ->useAttributeAsKey('key')
                    ->prototype('array')
                        ->children()
                            ->booleanNode('enabled')->defaultTrue()->end()
                            ->integerNode('time_period')->isRequired()->end() // microsecond
                            ->integerNode('maximum_number')->isRequired()->end()
                            ->integerNode('blocking_duration')->end() // second
                            ->arrayNode('group')
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(function($v) { return array($v); })
                                ->end()
                                ->prototype('scalar')->end()
                            ->end()
                            ->scalarNode('subject_class')->isRequired()->end()
                            ->arrayNode('subject_method')
                                ->ignoreExtraKeys()
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(function($v) { return [[$v]]; })
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('voter_default')->defaultTrue()->end()
                ->arrayNode('voter')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('class')->defaultValue('Avtonom\LimitNumberCallsBundle\Security\LimitCallsVoter')->end()
                    ->end()
                ->end()
                ->arrayNode('manager')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('class')->defaultValue('Avtonom\LimitNumberCallsBundle\Manager\LimitCallsManager')->end()
                    ->end()
                ->end()
                ->arrayNode('repository')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('class')->defaultValue('Avtonom\LimitNumberCallsBundle\Manager\LimitCallsRepository')->end()
                    ->end()
                ->end()
            ->end();
    }
}
