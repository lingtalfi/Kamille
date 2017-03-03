<?php


$params = [];
include __DIR__ . "/application-parameters.php";


$params = array_merge($params, [
    'app_dir' => realpath(__DIR__ . "/.."),
    'debug' => false,
]);