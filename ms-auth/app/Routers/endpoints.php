<?php

use App\Repositories\AuthRepository;
use Slim\App;

return function (App $app) {

    $app->post('/login', [AuthRepository::class, 'login']);
    $app->post('/logout', [AuthRepository::class, 'logout']);
    $app->get('/validar-token', [AuthRepository::class, 'validarToken']);
};