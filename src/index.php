<?php
namespace App;

use App\Middleware\CustomErrorHandler;
use App\validation\TaskValidation;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// In-memory data store for tasks
$tasks = [];

// Create Slim app
$app = AppFactory::create();
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler([CustomErrorHandler::class, 'handle']);

// Retrieve a list of tasks
$app->get('/tasks', function (Request $request, Response $response) use ($tasks) {
    return Utils::jsonResponse($tasks, $response);
});

// Retrieve details of a specific task
$app->get('/tasks/{id}', function (Request $request, Response $response, $args) use ($tasks) {
    $id = $args['id'];
    if (isset($tasks[$id])) {
        return Utils::jsonResponse($tasks[$id], $response);
    } else {
        return Utils::jsonResponse(['error' => 'Task not found'], $response, 404);
    }
});

// Add a new task
$app->post('/tasks', function (Request $request, Response $response) use ($tasks) {
    $data = $request->getParsedBody();

    // Validate request data
    $validationErrors = TaskValidation::validateTaskData($data);
    if (!empty($validationErrors)) {
        return Utils::jsonResponse(['error' => 'Validation failed', 'errors' => $validationErrors], $response, 400);
    }
    $task = [
        'id' => uniqid(),
        'title' => $data['title'],
        'description' => $data['description'],
        'status' => $data['status']
    ];
    $tasks[$task['id']] = $task;
    return Utils::jsonResponse($task, $response, 201);
});

// Update task details
$app->put('/tasks/{id}', function (Request $request, Response $response, $args) use ($tasks) {
    $id = $args['id'];
    if (isset($tasks[$id])) {
        $data = $request->getParsedBody();
        $task = $tasks[$id];
        $task['title'] = $data['title'];
        $task['description'] = $data['description'];
        $task['status'] = $data['status'];
        $validationErrors = TaskValidation::validateTaskData($tasks);
        if (!empty($validationErrors)) {
            return Utils::jsonResponse(['error' => 'Validation failed', 'errors' => $validationErrors], $response, 400);
        }
        $tasks[$id] = $task;
        return Utils::jsonResponse($task, $response);
    } else {
        return Utils::jsonResponse(['error' => 'Task not found'], $response, 404);
    }
});

// Delete a task
$app->delete('/tasks/{id}', function (Request $request, Response $response, $args) use ($tasks) {
    $id = $args['id'];
    if (isset($tasks[$id])) {
        unset($tasks[$id]);
        return $response->withStatus(204);
    } else {
        return Utils::jsonResponse(['error' => 'Task not found'], $response, 404);
    }
});

// Run the Slim app
$app->run();
