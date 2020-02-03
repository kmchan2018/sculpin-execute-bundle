<?php

$_ENV = getenv();
$key = $_SERVER['argv'][1] ?? 'HELLO';

if (array_key_exists($key, $_ENV)) {
    fprintf(STDOUT, "%s\n", $_ENV[$key]);
    exit(0);
} else {
    exit(1);
}
