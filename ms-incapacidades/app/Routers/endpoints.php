<?php

use App\Repositories\IncapacidadesRepository;
use Slim\App;

return function (App $app) {

    $app->get('/incapacidades', [IncapacidadesRepository::class, 'list']);
    $app->get('/incapacidad/{id}', [IncapacidadesRepository::class, 'detail']);
    $app->get('/incapacidades/empleado/{id}', [IncapacidadesRepository::class, 'porEmpleado']);
    $app->get('/incapacidades/estado/{estado}', [IncapacidadesRepository::class, 'porEstado']);
    $app->get('/incapacidades/tipo/{tipo}', [IncapacidadesRepository::class, 'porTipo']);
    $app->post('/incapacidad', [IncapacidadesRepository::class, 'create']);
    $app->put('/incapacidad/{id}', [IncapacidadesRepository::class, 'update']);
    $app->patch('/incapacidad/{id}/estado', [IncapacidadesRepository::class, 'cambiarEstado']);
};