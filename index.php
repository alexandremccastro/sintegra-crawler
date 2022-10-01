<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Crawler\PR as SintegraPR;

$crawler = new SintegraPR();

$crawler->prompt();
print_r($crawler->getResult());
