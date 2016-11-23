<?php

/**
 * @author Anton U <avtonomspb@gmail.com>
 */
namespace Avtonom\LimitNumberCallsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Definition\Processor;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class AvtonomLimitNumberCallsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->getManagerSection($container, $config);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if ($config['voter_default']) {
            $loader->load('default_anonymous_voter.yml');
            $secret = empty($config['firewalls']['anonymous']['secret']) ? time().uniqid() : $config['firewalls']['anonymous']['secret'];
            $container
                ->getDefinition('avtonom_limit_number_calls.voter')
                ->replaceArgument(2, $secret)
            ;
        }
    }

    /**
     *
     * @access private
     * @param $container, $config
     */
    private function getManagerSection($container, $config)
    {
        $container->setParameter('avtonom_limit_number_calls.voter.class', $config['voter']['class']);
        $container->setParameter('avtonom_limit_number_calls.repository.class', $config['repository']['class']);
        $container->setParameter('avtonom_limit_number_calls.manager.class', $config['manager']['class']);
    }
}
