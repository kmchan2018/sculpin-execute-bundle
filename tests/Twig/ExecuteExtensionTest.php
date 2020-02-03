<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\ExecuteBundle\Tests\Twig;

use Kmchan\Sculpin\ExecuteBundle\Twig\ExecuteExtension;
use PHPUnit\Framework\TestCase;

/**
 * This test case validates the Twig execute extension class.
 */
class ExecuteExtensionTest extends TestCase
{
    /**
     * Test if the extension will report error if the command invokes missing
     * command.
     */
    public function testInvalidCommand()
    {
        $result = $this->doExecute('xxx');
        $this->assertInstanceOf(\Exception::class, $result);
    }

    /**
     * Test if the extension can return standard output on successful execution
     * of a command.
     */
    public function testOnSuccessStdout()
    {
        $command = sprintf("%s %s/scripts/success.php", PHP_BINARY, __DIR__);
        $result = $this->doExecute($command, 'stdout', 'text:xxx');

        $this->assertIsString($result);
        $this->assertEquals("hello world\n", $result);
    }

    /**
     * Test if the extension can return standard error on successful execution
     * of a command.
     */
    public function testOnSuccessStderr()
    {
        $command = sprintf("%s %s/scripts/success.php", PHP_BINARY, __DIR__);
        $result = $this->doExecute($command, 'stderr', 'text:xxx');

        $this->assertIsString($result);
        $this->assertEquals("hello world\n", $result);
    }

    /**
     * Test if the extension can return hardcoded message on successful
     * execution of a command.
     */
    public function testOnSuccessText()
    {
        $command = sprintf("%s %s/scripts/success.php", PHP_BINARY, __DIR__);
        $result = $this->doExecute($command, 'text:hello world', 'text:xxx');

        $this->assertIsString($result);
        $this->assertEquals("hello world", $result);
    }

    /**
     * Test if the extension can throw exception on successful execution of a
     * command.
     */
    public function testOnSuccessException()
    {
        $command = sprintf("%s %s/scripts/success.php", PHP_BINARY, __DIR__);
        $result = $this->doExecute($command, 'exception:hello world', 'text:xxx');

        $this->assertInstanceOf(\Exception::class, $result);
        $this->assertEquals('hello world', $result->getMessage());
    }

    /**
     * Test if the extension can return standard output on failed execution of
     * a command.
     */
    public function testOnFailureStdout()
    {
        $command = sprintf("%s %s/scripts/failure.php", PHP_BINARY, __DIR__);
        $result = $this->doExecute($command, 'text:xxx', 'stdout');

        $this->assertIsString($result);
        $this->assertEquals("hello world\n", $result);
    }

    /**
     * Test if the extension can return standard error on failed execution of
     * a command.
     */
    public function testOnFailureStderr()
    {
        $command = sprintf("%s %s/scripts/failure.php", PHP_BINARY, __DIR__);
        $result = $this->doExecute($command, 'text:xxx', 'stderr');

        $this->assertIsString($result);
        $this->assertEquals("hello world\n", $result);
    }

    /**
     * Test if the extension can return hardcoded message on failed execution
     * of a command.
     */
    public function testOnFailureText()
    {
        $command = sprintf("%s %s/scripts/failure.php", PHP_BINARY, __DIR__);
        $result = $this->doExecute($command, 'text:xxx', 'text:hello world');

        $this->assertIsString($result);
        $this->assertEquals("hello world", $result);
    }

    /**
     * Test if the extension can throw exception on failed execution of a
     * command.
     */
    public function testOnFailureException()
    {
        $command = sprintf("%s %s/scripts/failure.php", PHP_BINARY, __DIR__);
        $result = $this->doExecute($command, 'text:xxx', 'exception:hello world');

        $this->assertInstanceOf(\Exception::class, $result);
        $this->assertEquals('hello world', $result->getMessage());
    }

    /**
     * Test if the extension can send input text to the standard input of
     * the child process when executing a command.
     */
    public function testInput()
    {
        $input = "Frodo\nSam\nMerry\nPippin\n\n";
        $command = sprintf("%s %s/scripts/echo.php", PHP_BINARY, __DIR__);
        $result = $this->doExecute($command, 'stdout', 'stdout', $input);

        $this->assertIsString($result);
        $this->assertEquals($input, $result);
    }

    /**
     * Test if the extension can retain the value of an existing environment
     * variable when executing a command.
     */
    public function testExistingEnv()
    {
        $home = getenv('HOME');
        $command = sprintf("%s %s/scripts/env.php HOME", PHP_BINARY, __DIR__);
        $result = $this->doExecute($command, 'stdout', 'text:missing');

        $this->assertIsString($result);
        $this->assertEquals("$home\n", $result);
    }

    /**
     * Test if the extension can inject a new environment variable when
     * executing a command.
     */
    public function testInjectEnv()
    {
        $variable = sprintf('VAR_%s_%d_%d', date('YmdHis'), getmypid(), mt_rand());
        $command = sprintf("%s %s/scripts/env.php %s", PHP_BINARY, __DIR__, $variable);
        $result = $this->doExecute($command, 'stdout', 'text:missing', '', [ $variable => $variable ]);

        $this->assertIsString($result);
        $this->assertEquals("$variable\n", $result);
    }

    /**
     * Test if the extension can override the value of an existing environment
     * variable when executing a command.
     */
    public function testOverrideEnv()
    {
        $command = sprintf("%s %s/scripts/env.php HOME", PHP_BINARY, __DIR__);
        $result = $this->doExecute($command, 'stdout', 'text:missing', '', [ 'HOME' => '/' ]);

        $this->assertIsString($result);
        $this->assertEquals("/\n", $result);
    }

    /**
     * Test if the extension can delete an existing environment variable when
     * executing a command.
     */
    public function testDeleteEnv()
    {
        $command = sprintf("%s %s/scripts/env.php HOME", PHP_BINARY, __DIR__);
        $result = $this->doExecute($command, 'stdout', 'text:missing', '', [ 'HOME' => false ]);

        $this->assertIsString($result);
        $this->assertEquals("missing", $result);
    }

    /**
     * Use the execution extension to run a command. Returns either the return
     * value or exception from the execute function of the extension.
     * @param string $command
     * @param string $on_success
     * @param string $on_failure
     * @param string $input
     * @param array|null $environment
     * @return string|Throwable
     */
    public function doExecute(
        string $command,
        string $on_success = 'stdout',
        string $on_failure = 'exception',
        string $input = '',
        array $environment = []
    ) {
        try {
            $extension = new ExecuteExtension($environment);
            return $extension->execute($input, $command, $on_success, $on_failure);
        } catch (\Throwable $ex) {
            return $ex;
        }
    }
}
