<?php

$output = null;

echo "Antes do passthru()" . PHP_EOL;
system("php parallel/parallel_function.php &", $output);
echo "depois do passthru()" . PHP_EOL;

echo "Antes do while" . PHP_EOL;
while(is_null($output)) {
    echo "On while teste" . PHP_EOL;
}
echo "depois do while" . PHP_EOL;