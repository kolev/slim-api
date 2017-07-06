<?php
// Routes

// $app->get('/[{name}]', function ($request, $response, $args) {
//     // Sample log message
//     $this->logger->info("Slim-Skeleton '/' route");
//
//     // Render index view
//     return $this->renderer->render($response, 'index.phtml', $args);
// });

$app->post('/auth/login', App\Controllers\AuthController::class . ':login');

$app->get('/tasks', App\Controllers\TasksController::class . ':index');
$app->get('/tasks/[{id}]', App\Controllers\TasksController::class . ':show');
$app->delete('/tasks/[{id}]', App\Controllers\TasksController::class . ':delete');
$app->post('/tasks', App\Controllers\TasksController::class . ':create');
$app->put('/tasks/[{id}]', App\Controllers\TasksController::class . ':update');
