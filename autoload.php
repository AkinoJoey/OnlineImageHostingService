<?php

spl_autoload_register(function ($name) {
    $filepath = __DIR__ . "/" . str_replace('\\', '/', $name) . ".php";
    require_once $filepath;
});
