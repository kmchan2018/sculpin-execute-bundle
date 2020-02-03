<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\ExecuteBundle\Tests\DependencyInjection;

use Kmchan\Sculpin\ExecuteBundle\DependencyInjection\SculpinExecuteExtension;
use PHPUnit\Framework\TestCase;

/**
 * This test case validates the bundle extension class.
 */
class SculpinExecuteExtensionTest extends TestCase
{
    /**
     * Test if the extension can extract environment variable from bundle
     * configuration correctly.
     */
    public function testExtractEnvironment(): void
    {
        $config = [
            'environment' => [
                'VAR1' => [ 'name' => 'VAR1', 'value' => 'var1' ],
                'VAR2' => [ 'name' => 'VAR2', 'value' => false ],
            ]
        ];

        $extension = new SculpinExecuteExtension();
        $environment = $extension->extractEnvironment($config);

        $this->assertIsArray($environment);
        $this->assertArrayHasKey('VAR1', $environment);
        $this->assertArrayHasKey('VAR2', $environment);
        $this->assertEquals('var1', $environment['VAR1']);
        $this->assertEquals(false, $environment['VAR2']);
    }
}
