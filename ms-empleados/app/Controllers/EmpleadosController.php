<?php

namespace App\Controllers;

use App\Models\Empleado;
use Exception;

class EmpleadosController
{
    function getEmpleados()
    {
        return Empleado::all();
    }

    function getEmpleado($id)
    {
        $empleado = Empleado::find($id);
        if (empty($empleado)) {
            throw new Exception("Empleado $id no existe", 2);
        }
        return $empleado;
    }

    function getEmpleadoPorDocumento($documento)
    {
        $empleado = Empleado::where('documento', $documento)->first();
        if (empty($empleado)) {
            throw new Exception("Empleado con documento $documento no existe", 2);
        }
        return $empleado;
    }

    function getEmpleadosPorArea($area)
    {
        $empleados = Empleado::where('area', $area)->get();
        if ($empleados->isEmpty()) {
            throw new Exception("No hay empleados en el area $area", 2);
        }
        return $empleados;
    }

    function getEmpleadosPorEstado($estado)
    {
        $empleados = Empleado::where('estado', $estado)->get();
        if ($empleados->isEmpty()) {
            throw new Exception("No hay empleados con estado $estado", 2);
        }
        return $empleados;
    }

    function guardarEmpleado($data)
    {
        if (
            empty($data['nombres'])      ||
            empty($data['apellidos'])    ||
            empty($data['documento'])    ||
            empty($data['correo'])       ||
            empty($data['telefono'])     ||
            empty($data['cargo'])        ||
            empty($data['area'])         ||
            empty($data['fecha_ingreso'])
        ) {
            throw new Exception("Faltan datos obligatorios", 1);
        }

        // Validar documento duplicado
        $docExiste = Empleado::where('documento', $data['documento'])->first();
        if (!empty($docExiste)) {
            throw new Exception("El documento ya existe", 3);
        }

        // Validar correo duplicado
        $correoExiste = Empleado::where('correo', $data['correo'])->first();
        if (!empty($correoExiste)) {
            throw new Exception("El correo ya existe", 4);
        }

        $empleado               = new Empleado();
        $empleado->nombres      = $data['nombres'];
        $empleado->apellidos    = $data['apellidos'];
        $empleado->documento    = $data['documento'];
        $empleado->correo       = $data['correo'];
        $empleado->telefono     = $data['telefono'];
        $empleado->cargo        = $data['cargo'];
        $empleado->area         = $data['area'];
        $empleado->fecha_ingreso = $data['fecha_ingreso'];
        $empleado->estado       = empty($data['estado']) ? 'activo' : $data['estado'];
        $empleado->save();

        return $empleado;
    }

    function modificarEmpleado($id, $data)
    {
        $empleado = $this->getEmpleado($id);

        $empleado->nombres      = $data['nombres'];
        $empleado->apellidos    = $data['apellidos'];
        $empleado->documento    = $data['documento'];
        $empleado->correo       = $data['correo'];
        $empleado->telefono     = $data['telefono'];
        $empleado->cargo        = $data['cargo'];
        $empleado->area         = $data['area'];
        $empleado->fecha_ingreso = $data['fecha_ingreso'];
        $empleado->estado       = empty($data['estado']) ? $empleado->estado : $data['estado'];
        $empleado->save();

        return $empleado;
    }

    function cambiarEstado($id, $data)
    {
        $empleado = $this->getEmpleado($id);

        $estadosValidos = ['activo', 'inactivo'];
        if (!in_array($data['estado'], $estadosValidos)) {
            throw new Exception("Estado invalido. Use: activo o inactivo", 1);
        }

        $empleado->estado = $data['estado'];
        $empleado->save();

        return $empleado;
    }

    function borrarEmpleado($id)
    {
        $empleado = $this->getEmpleado($id);
        $empleado->delete();
        return true;
    }
}