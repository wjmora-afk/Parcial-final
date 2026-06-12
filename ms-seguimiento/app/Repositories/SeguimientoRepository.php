<?php

namespace App\Repositories;

use App\Controllers\SeguimientoController;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SeguimientoRepository
{
    function list(Request $request, Response $response)
    {
        $controller   = new SeguimientoController();
        $seguimientos = $controller->getSeguimientos();
        $response->getBody()->write($seguimientos->toJson());
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json');
    }

    function detail(Request $request, Response $response, $args)
    {
        try {
            $controller  = new SeguimientoController();
            $seguimiento = $controller->getSeguimiento($args['id']);
            $response->getBody()->write($seguimiento->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => 'Seguimiento no encontrado']));
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function porIncapacidad(Request $request, Response $response, $args)
    {
        try {
            $controller   = new SeguimientoController();
            $seguimientos = $controller->getSeguimientosPorIncapacidad($args['id']);
            $response->getBody()->write($seguimientos->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => 'No hay seguimientos para esta incapacidad']));
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function create(Request $request, Response $response)
    {
        try {
            $body        = $request->getBody()->getContents();
            $data        = json_decode($body, true);
            $controller  = new SeguimientoController();
            $seguimiento = $controller->guardarSeguimiento($data);
            $response->getBody()->write($seguimiento->toJson());
            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $code = 400;
            if ($ex->getCode() == 1) {
                $code = 406;
                $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            } else {
                $response->getBody()->write(json_encode(['msg' => 'Error en el servicio']));
            }
            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function update(Request $request, Response $response, $args)
    {
        try {
            $body        = $request->getBody()->getContents();
            $data        = json_decode($body, true);
            $controller  = new SeguimientoController();
            $seguimiento = $controller->modificarSeguimiento($args['id'], $data);
            $response->getBody()->write($seguimiento->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
        }
    }
}