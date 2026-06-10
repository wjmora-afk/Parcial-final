<?php

namespace App\Repositories;

use App\Controllers\AuthController;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthRepository
{
    function login(Request $request, Response $response)
    {
        try {
            $body       = $request->getBody()->getContents();
            $data       = json_decode($body, true);
            $controller = new AuthController();
            $usuario    = $controller->login($data);
            $response->getBody()->write($usuario->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $code = 400;
            if ($ex->getCode() == 1) {
                $code = 406;
                $response->getBody()->write(json_encode(['msg' => 'Usuario y contraseña son obligatorios']));
            } elseif ($ex->getCode() == 2) {
                $code = 401;
                $response->getBody()->write(json_encode(['msg' => 'Credenciales incorrectas']));
            } elseif ($ex->getCode() == 3) {
                $code = 403;
                $response->getBody()->write(json_encode(['msg' => 'Usuario inactivo']));
            } else {
                $response->getBody()->write(json_encode(['msg' => 'Error en el servicio']));
            }
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function logout(Request $request, Response $response)
    {
        try {
            $body       = $request->getBody()->getContents();
            $data       = json_decode($body, true);
            $controller = new AuthController();
            $controller->logout($data['token']);
            $response->getBody()->write(json_encode(['msg' => 'Sesion cerrada correctamente']));
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $code = $ex->getCode() == 2 ? 401 : 400;
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function validarToken(Request $request, Response $response)
    {
        try {
            $token      = $request->getHeaderLine('Authorization');
            $controller = new AuthController();
            $usuario    = $controller->validarToken($token);
            $response->getBody()->write($usuario->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $code = $ex->getCode() == 2 ? 401 : 400;
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    }
}