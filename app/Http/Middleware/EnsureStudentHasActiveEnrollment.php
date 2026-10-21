<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Solo para el panel Alumno. El login en sí ya está bloqueado a nivel de
 * User::canAccessPanel() para quien no tiene matrícula activa (no se crea
 * sesión). Este middleware cubre el caso de una sesión YA iniciada a la que
 * se le inactiva la matrícula mientras el alumno sigue conectado: lo saca
 * del panel y lo manda a /no-matriculado.
 *
 * No cierra sesión (a diferencia de CheckUserStatus): apenas el admin le
 * cree/reactive una matrícula, el alumno recupera el acceso en su siguiente
 * clic, sin tener que volver a loguearse.
 */
class EnsureStudentHasActiveEnrollment
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && ! $user->tieneMatriculaActiva()) {
            return redirect()->route('no-matriculado');
        }

        return $next($request);
    }
}
