<?php

use App\Repositories\EmpleadosRepository;
use Slim\App;

return function (App $app) {

    $app->get('/empleados', [EmpleadosRepository::class, 'list']);
    $app->get('/empleado/{id}', [EmpleadosRepository::class, 'detail']);
    $app->get('/empleados/documento/{documento}', [EmpleadosRepository::class, 'porDocumento']);
    $app->get('/empleados/area/{area}', [EmpleadosRepository::class, 'porArea']);
    $app->get('/empleados/estado/{estado}', [EmpleadosRepository::class, 'porEstado']);
    $app->post('/empleado', [EmpleadosRepository::class, 'create']);
    $app->put('/empleado/{id}', [EmpleadosRepository::class, 'update']);
    $app->patch('/empleado/{id}/estado', [EmpleadosRepository::class, 'cambiarEstado']);
    $app->delete('/empleado/{id}', [EmpleadosRepository::class, 'delete']);
};