<?php

spl_autoload_register(function ($path) {
  $filename = 'src/' . join('/', explode('\\', $path)) . '.php';
  if (file_exists($filename)) include_once $filename;
}, true);
