<?php

namespace App\Repositories;

use App\Controllers\EmpleadosController;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EmpleadosRepository
{
    function list(Request $request, Response $response)
    {
        $controller = new EmpleadosController();
        $empleados  = $controller->getEmpleados();
        $response->getBody()->write($empleados->toJson());
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json');
    }

    function detail(Request $request, Response $response, $args)
    {
        try {
            $controller = new EmpleadosController();
            $empleado   = $controller->getEmpleado($args['id']);
            $response->getBody()->write($empleado->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => 'Empleado no encontrado']));
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function porDocumento(Request $request, Response $response, $args)
    {
        try {
            $controller = new EmpleadosController();
            $empleado   = $controller->getEmpleadoPorDocumento($args['documento']);
            $response->getBody()->write($empleado->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => 'Empleado no encontrado']));
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function porArea(Request $request, Response $response, $args)
    {
        try {
            $controller = new EmpleadosController();
            $empleados  = $controller->getEmpleadosPorArea($args['area']);
            $response->getBody()->write($empleados->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => 'No hay empleados en esta area']));
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function porEstado(Request $request, Response $response, $args)
    {
        try {
            $controller = new EmpleadosController();
            $empleados  = $controller->getEmpleadosPorEstado($args['estado']);
            $response->getBody()->write($empleados->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => 'No hay empleados con este estado']));
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function create(Request $request, Response $response)
    {
        try {
            $body       = $request->getBody()->getContents();
            $data       = json_decode($body, true);
            $controller = new EmpleadosController();
            $empleado   = $controller->guardarEmpleado($data);
            $response->getBody()->write($empleado->toJson());
            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $code = 400;
            if ($ex->getCode() == 1) {
                $code = 406;
                $response->getBody()->write(json_encode(['msg' => 'Faltan datos obligatorios']));
            } elseif ($ex->getCode() == 3) {
                $code = 409;
                $response->getBody()->write(json_encode(['msg' => 'El documento ya existe']));
            } elseif ($ex->getCode() == 4) {
                $code = 409;
                $response->getBody()->write(json_encode(['msg' => 'El correo ya existe']));
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
            $body       = $request->getBody()->getContents();
            $data       = json_decode($body, true);
            $controller = new EmpleadosController();
            $empleado   = $controller->modificarEmpleado($args['id'], $data);
            $response->getBody()->write($empleado->toJson());
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => 'Empleado no encontrado']));
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    function cambiarEstado(Request $request, Response $response, $args)
    {
        try {
            $body       = $request->getBody()->getContents();
            $data       = json_decode($body, true);
            $controller = new EmpleadosController();
            $empleado   = $controller->cambiarEstado($args['id'], $data);
            $response->getBody()->write($empleado->toJson());
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

    function delete(Request $request, Response $response, $args)
    {
        try {
            $controller = new EmpleadosController();
            $controller->borrarEmpleado($args['id']);
            $response->getBody()->write(json_encode(['msg' => 'Empleado eliminado']));
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $response->getBody()->write(json_encode(['msg' => 'Empleado no encontrado']));
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
        }
    }
}