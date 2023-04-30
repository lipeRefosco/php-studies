<?php

$output;

exec("ls -R", $output);

print_r($output);