<?php

while (feof(STDIN) === false) {
    fwrite(STDOUT, fread(STDIN, 1024));
}
