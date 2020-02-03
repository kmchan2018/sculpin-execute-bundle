<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\ExecuteBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Symfony dependency injection extension to register our twig extension to
 * twig template engine.
 */
class SculpinExecuteExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('sculpin_execute.environment', $this->extractEnvironment($config));
    }

    /**
     * Extract the environment variable from bundle config.
     * @param array $config
     * @return array
     */
    public function extractEnvironment(array $config): array
    {
        $environment = [];

        if (array_key_exists('environment', $config)) {
            foreach ($config['environment'] as $key => $value) {
                $environment[$key] = $value['value'];
            }
        }

        return $environment;
    }
}
