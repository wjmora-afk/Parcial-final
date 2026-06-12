<?php

use App\Repositories\SeguimientoRepository;
use Slim\App;

return function (App $app) {

    $app->get('/seguimientos', [SeguimientoRepository::class, 'list']);
    $app->get('/seguimiento/{id}', [SeguimientoRepository::class, 'detail']);
    $app->get('/seguimientos/incapacidad/{id}', [SeguimientoRepository::class, 'porIncapacidad']);
    $app->post('/seguimiento', [SeguimientoRepository::class, 'create']);
    $app->put('/seguimiento/{id}', [SeguimientoRepository::class, 'update']);
};