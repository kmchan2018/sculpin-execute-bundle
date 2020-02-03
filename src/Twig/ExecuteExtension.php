<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\ExecuteBundle\Twig;

use Exception;
use InvalidArgumentException;
use Throwable;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

/**
 * Twig extension that provides an extra twig function `execute` for executing
 * command and returning its result for embeddeding.
 */
class ExecuteExtension extends AbstractExtension
{
    /**
     * @var array
     */
    private $environment;

    /**
     * Construct a new extension.
     */
    public function __construct(array $environment = [])
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('execute', [ $this, 'execute' ]),
        ];
    }

    /**
     * Execute the command and send the input to it via stdin. Perform the
     * action specified in `$on_success` argument if the execution has
     * succeed; otherwise perform the action specified in `$on_failure`
     * argument.
     *
     * Action is specified as a string. It consists of action type, optionally
     * followed by `:` separator and then its parameter.
     *
     * There are a few recognized action type. The `stdout` action returns
     * output messages from the standard output stream. The `stderr` action
     * returns output messages from the standard error stream. The `text`
     * action returns hardcoded text message given by the parameter. The
     * `exception` action throws an exception; its message can be customized
     * by the optional parameter.
     *
     * @param string $input Input to the command.
     * @param string $command Command to execute.
     * @param string $on_success Action when the execution has succeeded.
     * @param string $on_failure Action when the execution has failed.
     * @return string Result from action.
     * @throws Throwable Result from action.
     * @throws InvalidArgumentException if the action is not supported.
     */
    public function execute(string $input, string $command, string $on_success = 'stdout', string $on_failure = 'exception'): string
    {
        $process = Process::fromShellCommandline($command, null, $this->environment);
        $action = $on_success;
        $reason = '';

        try {
            $process->setInput($input);
            $process->run();

            if ($process->getExitCode() === 0) {
                $action = $on_success;
            } else {
                $action = $on_failure;
                $reason = 'command returns non-zero exit code';
            }
        } catch (\Throwable $ex) {
            $action = $on_failure;
            $reason = $ex->getMessage();
        }

        if (strcmp($action, 'stdout') === 0 || strncmp($action, 'stdout:', 7) === 0) {
            return $process->getOutput();
        } elseif (strcmp($action, 'stderr') === 0 || strncmp($action, 'stderr:', 7) === 0) {
            return $process->getErrorOutput();
        } elseif (strncmp($action, 'text:', 5) === 0) {
            return substr($action, 5);
        } elseif (strcmp($action, 'exception') === 0) {
            throw new \Exception("cannot execute command $command: $reason");
        } elseif (strncmp($action, 'exception:', 10) === 0) {
            throw new \Exception(substr($action, 10));
        } else {
            throw new \InvalidArgumentException("Unknown action $action");
        }
    }
}
