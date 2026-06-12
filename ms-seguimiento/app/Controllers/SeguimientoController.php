<?php

namespace App\Controllers;

use App\Models\Seguimiento;
use Exception;

class SeguimientoController
{
    function getSeguimientos()
    {
        return Seguimiento::all();
    }

    function getSeguimiento($id)
    {
        $seguimiento = Seguimiento::find($id);
        if (empty($seguimiento)) {
            throw new Exception("Seguimiento $id no existe", 2);
        }
        return $seguimiento;
    }

    function getSeguimientosPorIncapacidad($incapacidadId)
    {
        $seguimientos = Seguimiento::where('incapacidad_id', $incapacidadId)
            ->orderBy('created_at', 'asc')
            ->get();
        if ($seguimientos->isEmpty()) {
            throw new Exception("No hay seguimientos para la incapacidad $incapacidadId", 2);
        }
        return $seguimientos;
    }

    function guardarSeguimiento($data)
    {
        if (
            empty($data['incapacidad_id'])      ||
            empty($data['fecha'])               ||
            empty($data['comentario'])          ||
            empty($data['estado'])              ||
            empty($data['usuario_responsable'])
        ) {
            throw new Exception("Faltan datos obligatorios", 1);
        }

        $estadosValidos = ['registrada', 'en_revision', 'aprobada', 'rechazada', 'finalizada'];
        if (!in_array($data['estado'], $estadosValidos)) {
            throw new Exception("Estado invalido", 1);
        }

        $seguimiento                      = new Seguimiento();
        $seguimiento->incapacidad_id      = $data['incapacidad_id'];
        $seguimiento->fecha               = $data['fecha'];
        $seguimiento->comentario          = $data['comentario'];
        $seguimiento->estado              = $data['estado'];
        $seguimiento->usuario_responsable = $data['usuario_responsable'];
        $seguimiento->save();

        return $seguimiento;
    }

    function modificarSeguimiento($id, $data)
    {
        $seguimiento = $this->getSeguimiento($id);

        if (empty($data['comentario']) || empty($data['estado']) || empty($data['usuario_responsable'])) {
            throw new Exception("Faltan datos obligatorios", 1);
        }

        $estadosValidos = ['registrada', 'en_revision', 'aprobada', 'rechazada', 'finalizada'];
        if (!in_array($data['estado'], $estadosValidos)) {
            throw new Exception("Estado invalido", 1);
        }

        $seguimiento->comentario          = $data['comentario'];
        $seguimiento->estado              = $data['estado'];
        $seguimiento->usuario_responsable = $data['usuario_responsable'];
        $seguimiento->save();

        return $seguimiento;
    }
}