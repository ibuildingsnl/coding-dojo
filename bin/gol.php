<?php

require 'vendor/autoload.php';

$gol = new \CodingDojo\GameOfLifeCliApplication(array_slice($argv, 1));
$gol->execute(10000);
