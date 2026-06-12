<?php

namespace App\Repositories;

use App\Controllers\IncapacidadesController;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class IncapacidadesRepository
{
    function list(Request $request, Response $response)
    {
        $controller    = new IncapacidadesController();
        $incapacidades = $controller->getIncapacidades();
        $response->getBody()->write($incapacidades->toJson());
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json');
    }

    function detail(Request $request, Response $response, $args)
    {
        try {
            $controller  = new IncapacidadesController();
            $incapacidad = $controller->getIncapacidad($args['id']);
            $response->getBody()->write($incapacidad->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => 'Incapacidad no encontrada']));
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function porEmpleado(Request $request, Response $response, $args)
    {
        try {
            $controller    = new IncapacidadesController();
            $incapacidades = $controller->getIncapacidadesPorEmpleado($args['id']);
            $response->getBody()->write($incapacidades->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => 'No hay incapacidades para este empleado']));
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function porEstado(Request $request, Response $response, $args)
    {
        try {
            $controller    = new IncapacidadesController();
            $incapacidades = $controller->getIncapacidadesPorEstado($args['estado']);
            $response->getBody()->write($incapacidades->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => 'No hay incapacidades con este estado']));
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function porTipo(Request $request, Response $response, $args)
    {
        try {
            $controller    = new IncapacidadesController();
            $incapacidades = $controller->getIncapacidadesPorTipo($args['tipo']);
            $response->getBody()->write($incapacidades->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => 'No hay incapacidades de este tipo']));
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function create(Request $request, Response $response)
    {
        try {
            $body          = $request->getBody()->getContents();
            $data          = json_decode($body, true);
            $controller    = new IncapacidadesController();
            $incapacidad   = $controller->guardarIncapacidad($data);
            $response->getBody()->write($incapacidad->toJson());
            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $code = 400;
            if ($ex->getCode() == 1) {
                $code = 406;
                $response->getBody()->write(json_encode(['msg' => $ex->getMessage()]));
            } elseif ($ex->getCode() == 3) {
                $code = 409;
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
            $controller  = new IncapacidadesController();
            $incapacidad = $controller->modificarIncapacidad($args['id'], $data);
            $response->getBody()->write($incapacidad->toJson());
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

    function cambiarEstado(Request $request, Response $response, $args)
    {
        try {
            $body        = $request->getBody()->getContents();
            $data        = json_decode($body, true);
            $controller  = new IncapacidadesController();
            $incapacidad = $controller->cambiarEstado($args['id'], $data);
            $response->getBody()->write($incapacidad->toJson());
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