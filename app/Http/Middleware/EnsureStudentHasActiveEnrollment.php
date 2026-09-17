<?php

namespace App\Http\Middleware;

use App\Enums\EstadoMatricula;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Solo para el panel Alumno: si el usuario autenticado tiene un perfil de
 * Student pero ninguna matrícula (Inscription) con estado_matricula =
 * "activa", se le saca del panel y se le manda a /no-matriculado.
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

        if ($user) {
            $tieneMatriculaActiva = $user->student
                ?->inscripciones()
                ->where('estado_matricula', EstadoMatricula::ACTIVA->value)
                ->exists() ?? false;

            if (! $tieneMatriculaActiva) {
                return redirect()->route('no-matriculado');
            }
        }

        return $next($request);
    }
}
