<?php

define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_CHECK', true);
define('PUBLIC_AJAX_MODE', true);
define('STOP_WEBDAV', true);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use FastRoute\RouteCollector as RouteCollector;
use function FastRoute\simpleDispatcher;

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $r->addGroup('/api/v1', function (RouteCollector $r) {
        $r->addGroup('/users', function (RouteCollector $r) {
            $r->post('/login/', [function () {
                $c = new \App\Controller\Users;
                return $c->login();
            }, 'PUBLIC']);
        });
        $r->addGroup('/trainings', function (RouteCollector $r) {
            $r->get('/', [function ($id) {
                $c = new \App\Controller\Trainings;
                return $c->getList();
            }, 'PRIVATE']);

            $r->get('/{id:[0-9\-]+}/', [function ($id) {
                $c = new \App\Controller\Trainings;
                return $c->getById($id);
            }, 'PRIVATE']);

            $r->post('/add/', [function () {
                $c = new \App\Controller\Trainings;
                return $c->create([]);
            }, 'PRIVATE']);

            $r->post('/{id:[0-9\-]+}/delete/', [function ($id) {
                $c = new \App\Controller\Trainings;
                return $c->delete($id);
            }, 'PRIVATE']);

            $r->post('/{id:[0-9\-]+}/update/', [function ($id) {
                $c = new \App\Controller\Trainings;
                return $c->update($id);
            }, 'PRIVATE']);
        });
        $r->addGroup('/enrollment', function (RouteCollector $r) {
            $r->get('/', [function () {
                $c = new \App\Controller\Enrollment;
                return $c->getList();
            }, 'PRIVATE']);

            $r->post('/add/', [function () {
                $c = new \App\Controller\Enrollment;
                return $c->create([]);
            }, 'PRIVATE']);

            $r->post('/delete/', [function () {
                $c = new \App\Controller\Enrollment;
                return $c->delete();
            }, 'PRIVATE']);
        });
    });
});

require_once('dispatch.php');
