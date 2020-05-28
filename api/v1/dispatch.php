<?php

use App\Common\Response;
use App\Model\User;

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo json_encode(
            Response::error('Route Not Found', 404),
            JSON_UNESCAPED_UNICODE
        );
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo json_encode(
            Response::error('Method Not Allowed', 405),
            JSON_UNESCAPED_UNICODE
        );
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1][0];
        $vars = $routeInfo[2];

        $user = new User;
        if ($routeInfo[1][1] && $routeInfo[1][1] === 'PRIVATE' && !$user->isCredentialsValid()) {
            echo json_encode(
                Response::error('The request requires an user authentication', 401),
                JSON_UNESCAPED_UNICODE
            );
            return;
        }

        try {
            $result = call_user_func_array($handler, $vars);
            if ($result === null) {
                echo json_encode(
                    Response::error('Route Not Found', 404),
                    JSON_UNESCAPED_UNICODE
                );
                return;
            }
        } catch (Exception $e) {
            if ($e->getCode() == 403) {
                echo json_encode(
                    Response::error($e->getMessage(), 403),
                    JSON_UNESCAPED_UNICODE
                );
                return;
            }
            if ($e->getCode() == 404) {
                echo json_encode(
                    Response::error($e->getMessage(), 404),
                    JSON_UNESCAPED_UNICODE
                );
                return;
            }
            echo json_encode(
                Response::error($e->getMessage()),
                JSON_UNESCAPED_UNICODE
            );
            return;
        }

        if ($routeInfo[1][2] && $routeInfo[1][2] === 'MANUAL_CONTROL') {
            return;
        }

        header('Content-Type: text/html; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Content-Type, origin");
        header("HTTP/1.0 200 OK");

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        break;
    default:
        echo json_encode(
            Response::error('I\'m a teapot', 418),
            JSON_UNESCAPED_UNICODE
        );
}
