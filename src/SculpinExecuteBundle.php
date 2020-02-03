<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\ExecuteBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Sculpin Bundle to implement a twig extension that executes a command,
 * send data to it via stdin and displays output from stdout.
 */
class SculpinExecuteBundle extends Bundle
{
    // empty
}
