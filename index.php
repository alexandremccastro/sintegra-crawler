<?php

include_once 'autoload.php';

use Core\Crawler\PR as SintegraPR;

$crawler = new SintegraPR();

$crawler->prompt();
var_dump($crawler->getResults());
