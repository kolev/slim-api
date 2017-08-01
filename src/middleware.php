<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

$app->add(new \Tuupola\Middleware\Cors([
    "origin" => ["*"],
    "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE"],
    "headers.allow" => ["Authorization"],
    "headers.expose" => [],
    "credentials" => false,
    "cache" => 0,
]));

$app->add(new \App\TokenAuth($app->getContainer()));
