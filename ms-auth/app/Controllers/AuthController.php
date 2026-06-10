<?php

namespace App\Controllers;

use App\Models\Usuario;
use Exception;

class AuthController
{
    function login($data)
    {
        if (empty($data['usuario']) || empty($data['contrasena'])) {
            throw new Exception("Usuario y contraseña son obligatorios", 1);
        }

        $usuario = Usuario::where('usuario', $data['usuario'])
            ->orWhere('correo', $data['usuario'])
            ->first();

        if (empty($usuario)) {
            throw new Exception("Credenciales incorrectas", 2);
        }

        if ($usuario->contrasena !== $data['contrasena']) {
            throw new Exception("Credenciales incorrectas", 2);
        }

        if ($usuario->estado === 'inactivo') {
            throw new Exception("Usuario inactivo", 3);
        }

        // Generar token simple
        $token = bin2hex(random_bytes(32));

        $usuario->token         = $token;
        $usuario->sesion_activa = true;
        $usuario->save();

        return $usuario;
    }

    function logout($token)
    {
        if (empty($token)) {
            throw new Exception("Token requerido", 1);
        }

        $usuario = Usuario::where('token', $token)->first();

        if (empty($usuario)) {
            throw new Exception("Token invalido", 2);
        }

        $usuario->token         = null;
        $usuario->sesion_activa = false;
        $usuario->save();

        return true;
    }

    function validarToken($token)
    {
        if (empty($token)) {
            throw new Exception("Token requerido", 1);
        }

        $usuario = Usuario::where('token', $token)
            ->where('sesion_activa', true)
            ->first();

        if (empty($usuario)) {
            throw new Exception("Token invalido o sesion expirada", 2);
        }

        return $usuario;
    }
}