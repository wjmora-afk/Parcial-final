<?php

namespace App\Controllers;

use App\Models\Incapacidad;
use Exception;

class IncapacidadesController
{
    function getIncapacidades()
    {
        return Incapacidad::all();
    }

    function getIncapacidad($id)
    {
        $incapacidad = Incapacidad::find($id);
        if (empty($incapacidad)) {
            throw new Exception("Incapacidad $id no existe", 2);
        }
        return $incapacidad;
    }

    function getIncapacidadesPorEmpleado($empleadoId)
    {
        $incapacidades = Incapacidad::where('empleado_id', $empleadoId)->get();
        if ($incapacidades->isEmpty()) {
            throw new Exception("No hay incapacidades para el empleado $empleadoId", 2);
        }
        return $incapacidades;
    }

    function getIncapacidadesPorEstado($estado)
    {
        $incapacidades = Incapacidad::where('estado', $estado)->get();
        if ($incapacidades->isEmpty()) {
            throw new Exception("No hay incapacidades con estado $estado", 2);
        }
        return $incapacidades;
    }

    function getIncapacidadesPorTipo($tipo)
    {
        $incapacidades = Incapacidad::where('tipo', $tipo)->get();
        if ($incapacidades->isEmpty()) {
            throw new Exception("No hay incapacidades de tipo $tipo", 2);
        }
        return $incapacidades;
    }

    function guardarIncapacidad($data)
    {
        if (
            empty($data['empleado_id'])        ||
            empty($data['fecha_inicio'])        ||
            empty($data['fecha_fin'])           ||
            empty($data['tipo'])               ||
            empty($data['diagnostico_general']) ||
            empty($data['entidad_medica'])
        ) {
            throw new Exception("Faltan datos obligatorios", 1);
        }

        // Validar fechas
        if ($data['fecha_fin'] < $data['fecha_inicio']) {
            throw new Exception("La fecha fin no puede ser menor a la fecha inicio", 1);
        }

        // Calcular dias automaticamente
        $inicio = new \DateTime($data['fecha_inicio']);
        $fin    = new \DateTime($data['fecha_fin']);
        $dias   = $inicio->diff($fin)->days + 1;

        // Validar incapacidad duplicada para el mismo empleado y rango de fechas
        $duplicada = Incapacidad::where('empleado_id', $data['empleado_id'])
            ->where('fecha_inicio', $data['fecha_inicio'])
            ->where('fecha_fin', $data['fecha_fin'])
            ->first();

        if (!empty($duplicada)) {
            throw new Exception("Ya existe una incapacidad para este empleado en ese rango de fechas", 3);
        }

        $tiposValidos = ['enfermedad_general', 'accidente_laboral', 'licencia_medica', 'incapacidad_temporal'];
        if (!in_array($data['tipo'], $tiposValidos)) {
            throw new Exception("Tipo invalido", 1);
        }

        $incapacidad                      = new Incapacidad();
        $incapacidad->empleado_id         = $data['empleado_id'];
        $incapacidad->fecha_inicio        = $data['fecha_inicio'];
        $incapacidad->fecha_fin           = $data['fecha_fin'];
        $incapacidad->tipo                = $data['tipo'];
        $incapacidad->diagnostico_general = $data['diagnostico_general'];
        $incapacidad->entidad_medica      = $data['entidad_medica'];
        $incapacidad->observaciones       = empty($data['observaciones']) ? null : $data['observaciones'];
        $incapacidad->dias_incapacidad    = $dias;
        $incapacidad->estado              = empty($data['estado']) ? 'registrada' : $data['estado'];
        $incapacidad->save();

        return $incapacidad;
    }

    function modificarIncapacidad($id, $data)
    {
        $incapacidad = $this->getIncapacidad($id);

        if ($data['fecha_fin'] < $data['fecha_inicio']) {
            throw new Exception("La fecha fin no puede ser menor a la fecha inicio", 1);
        }

        $inicio = new \DateTime($data['fecha_inicio']);
        $fin    = new \DateTime($data['fecha_fin']);
        $dias   = $inicio->diff($fin)->days + 1;

        $incapacidad->fecha_inicio        = $data['fecha_inicio'];
        $incapacidad->fecha_fin           = $data['fecha_fin'];
        $incapacidad->observaciones       = empty($data['observaciones']) ? null : $data['observaciones'];
        $incapacidad->estado              = empty($data['estado']) ? $incapacidad->estado : $data['estado'];
        $incapacidad->dias_incapacidad    = $dias;
        $incapacidad->save();

        return $incapacidad;
    }

    function cambiarEstado($id, $data)
    {
        $incapacidad = $this->getIncapacidad($id);

        $estadosValidos = ['registrada', 'en_revision', 'aprobada', 'rechazada', 'finalizada'];
        if (!in_array($data['estado'], $estadosValidos)) {
            throw new Exception("Estado invalido", 1);
        }

        $incapacidad->estado = $data['estado'];
        $incapacidad->save();

        return $incapacidad;
    }
}