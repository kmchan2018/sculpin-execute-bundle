<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\ExecuteBundle\Tests\DependencyInjection;

use Kmchan\Sculpin\ExecuteBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

/**
 * This test case validates the bundle configuration class.
 */
class ConfigurationTest extends TestCase
{
    /**
     * Test if the configuration can validate a single configuration tree
     * correctly.
     */
    public function testBasicConfiguration(): void
    {
        $configs = [
            [
                'environment' => [
                    [ 'name' => 'VAR1', 'value' => 'var1' ],
                    [ 'name' => 'VAR2', 'value' => false ],
                ]
            ]
        ];

        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);

        $this->assertIsArray($config);
        $this->assertArrayHasKey('environment', $config);

        $this->assertIsArray($config['environment']);
        $this->assertCount(2, $config['environment']);

        $this->assertArrayHasKey('VAR1', $config['environment']);
        $this->assertIsArray($config['environment']['VAR1']);
        $this->assertArrayHasKey('value', $config['environment']['VAR1']);
        $this->assertEquals('VAR1', $config['environment']['VAR1']['name']);
        $this->assertEquals('var1', $config['environment']['VAR1']['value']);

        $this->assertArrayHasKey('VAR2', $config['environment']);
        $this->assertIsArray($config['environment']['VAR2']);
        $this->assertArrayHasKey('value', $config['environment']['VAR2']);
        $this->assertEquals('VAR2', $config['environment']['VAR2']['name']);
        $this->assertEquals(false, $config['environment']['VAR2']['value']);
    }

    /**
     * Test if the configuration can validate and merge multiple configuration
     * trees correctly.
     */
    public function testMergeConfiguration(): void
    {
        $configs = [
            [
                'environment' => [
                    [ 'name' => 'VAR1', 'value' => 'var1' ],
                    [ 'name' => 'VAR2', 'value' => false ],
                ]
            ],
            [
                'environment' => [
                    [ 'name' => 'VAR2', 'value' => 'var2' ],
                    [ 'name' => 'VAR3', 'value' => false ],
                ]
            ]
        ];

        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);

        $this->assertIsArray($config);
        $this->assertArrayHasKey('environment', $config);

        $this->assertIsArray($config['environment']);
        $this->assertCount(3, $config['environment']);

        $this->assertArrayHasKey('VAR1', $config['environment']);
        $this->assertIsArray($config['environment']['VAR1']);
        $this->assertArrayHasKey('value', $config['environment']['VAR1']);
        $this->assertEquals('VAR1', $config['environment']['VAR1']['name']);
        $this->assertEquals('var1', $config['environment']['VAR1']['value']);

        $this->assertArrayHasKey('VAR2', $config['environment']);
        $this->assertIsArray($config['environment']['VAR2']);
        $this->assertArrayHasKey('value', $config['environment']['VAR2']);
        $this->assertEquals('VAR2', $config['environment']['VAR2']['name']);
        $this->assertEquals('var2', $config['environment']['VAR2']['value']);

        $this->assertArrayHasKey('VAR3', $config['environment']);
        $this->assertIsArray($config['environment']['VAR3']);
        $this->assertArrayHasKey('value', $config['environment']['VAR3']);
        $this->assertEquals('VAR3', $config['environment']['VAR3']['name']);
        $this->assertEquals(false, $config['environment']['VAR3']['value']);
    }
}
