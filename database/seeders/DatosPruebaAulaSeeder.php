<?php

namespace Database\Seeders;

use App\Enums\EstadoMatricula;
use App\Models\AcademicCycle;
use App\Models\Area;
use App\Models\CicloCourse;
use App\Models\CicloCourseTeacher;
use App\Models\Course;
use App\Models\Inscription;
use App\Models\Student;
use App\Models\TeacherCourseContent;
use App\Models\Teacher;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * DATOS DE PRUEBA para el aula virtual. NO está en DatabaseSeeder y no debe
 * ejecutarse en producción.
 *
 * Escenario:
 *  - Dos ciclos ACTIVOS a la vez ("PRUEBA CICLO 1" y "PRUEBA CICLO 2").
 *  - Un mismo curso ("Matemática Prueba") en ambos ciclos, con docente y
 *    contenido distinto en cada uno.
 *  - Docente con turno Noche en ambos ciclos y turno Mañana en el ciclo 2.
 *  - alumno.prueba: matrícula activa en Noche de ambos ciclos (2 matrículas).
 *  - alumno.prueba2: matrícula activa en Mañana del ciclo 2.
 *
 * Contraseña de todas las cuentas: "password". Idempotente: se puede correr
 * varias veces sin duplicar nada.
 *
 * php artisan db:seed --class=DatosPruebaAulaSeeder
 */
class DatosPruebaAulaSeeder extends Seeder
{
    private int $autor;

    public function run(): void
    {
        // Los modelos guardan user_create_id desde el usuario autenticado
        $admin = User::role('Administrador')->first() ?? User::firstOrFail();
        Auth::setUser($admin);
        $this->autor = $admin->id;

        $area = Area::firstOrCreate(
            ['nombre' => 'Área Prueba'],
            ['estado' => true, 'user_create_id' => $this->autor]
        );

        $ciclo1 = $this->ciclo('PRUEBA CICLO 1', 1);
        $ciclo2 = $this->ciclo('PRUEBA CICLO 2', 2);

        $manana = Turno::where('nombre', 'Mañana')->firstOrFail();
        $noche = Turno::where('nombre', 'Noche')->firstOrFail();

        $curso = Course::firstOrCreate(
            ['codigo' => 'MAT-PRU'],
            ['nombre' => 'Matemática Prueba', 'area_id' => $area->id, 'estado' => 'activo', 'horas_semanales' => 4, 'imagen_path' => '', 'user_create_id' => $this->autor]
        );

        // Docentes
        $docente = $this->usuario('docente.prueba@cepre.test', 'Docente Prueba', 'Profesor');
        $docenteModel = Teacher::firstOrCreate(
            ['user_id' => $docente->id],
            ['dni' => '90000001', 'estado' => true]
        );

        $docente2 = $this->usuario('docente2.prueba@cepre.test', 'Docente Prueba 2', 'Profesor');
        $docente2Model = Teacher::firstOrCreate(
            ['user_id' => $docente2->id],
            ['dni' => '90000002', 'estado' => true]
        );

        // Curso en cada ciclo + asignaciones (docente, turno)
        $cc1 = $this->cicloCourse($ciclo1, $curso);
        $cc2 = $this->cicloCourse($ciclo2, $curso);

        $asignaciones = [
            ['cc' => $cc1, 'docente' => $docenteModel, 'turno' => $noche, 'etiqueta' => 'PRUEBA CICLO 1 · Noche'],
            ['cc' => $cc2, 'docente' => $docenteModel, 'turno' => $noche, 'etiqueta' => 'PRUEBA CICLO 2 · Noche'],
            ['cc' => $cc2, 'docente' => $docente2Model, 'turno' => $manana, 'etiqueta' => 'PRUEBA CICLO 2 · Mañana'],
        ];

        foreach ($asignaciones as $a) {
            $cct = CicloCourseTeacher::firstOrCreate(
                ['ciclo_course_id' => $a['cc']->id, 'teacher_id' => $a['docente']->id, 'turno_id' => $a['turno']->id],
                ['estado' => true, 'user_create_id' => $this->autor]
            );

            // Contenido distinto por asignación para comprobar que no se mezcla
            TeacherCourseContent::firstOrCreate(
                ['ciclo_course_teacher_id' => $cct->id, 'titulo' => 'Semana 01 · ' . $a['etiqueta']],
                ['descripcion' => 'Contenido exclusivo de ' . $a['etiqueta'], 'orden' => 1, 'estado' => true, 'user_create_id' => $this->autor]
            );
        }

        // Alumnos
        $alumno = $this->usuario('alumno.prueba@cepre.test', 'Alumno Prueba', 'Alumno');
        $alumnoModel = Student::firstOrCreate(
            ['user_id' => $alumno->id],
            ['apellidos' => 'Prueba Dos Matriculas', 'dni' => '90000003', 'estado' => true, 'user_create_id' => $this->autor]
        );

        $alumno2 = $this->usuario('alumno2.prueba@cepre.test', 'Alumno Prueba 2', 'Alumno');
        $alumno2Model = Student::firstOrCreate(
            ['user_id' => $alumno2->id],
            ['apellidos' => 'Prueba Turno Manana', 'dni' => '90000004', 'estado' => true, 'user_create_id' => $this->autor]
        );

        $this->matricula($alumnoModel, $ciclo1, $noche);
        $this->matricula($alumnoModel, $ciclo2, $noche);
        $this->matricula($alumno2Model, $ciclo2, $manana);
    }

    private function ciclo(string $nombre, int $numero): AcademicCycle
    {
        return AcademicCycle::firstOrCreate(
            ['nombre' => $nombre],
            [
                'fecha_inicio' => now()->subDays(5)->toDateString(),
                'fecha_fin' => now()->addMonths(3)->toDateString(),
                'año' => now()->year,
                'numero' => $numero,
                'precio' => 0,
                'estado' => true,
                'user_create_id' => $this->autor,
            ]
        );
    }

    private function cicloCourse(AcademicCycle $ciclo, Course $curso): CicloCourse
    {
        return CicloCourse::firstOrCreate(
            ['ciclo_id' => $ciclo->id, 'course_id' => $curso->id],
            ['estado' => true, 'user_create_id' => $this->autor]
        );
    }

    private function usuario(string $email, string $nombre, string $rol): User
    {
        // "estado" es texto ('activo'/'inactivo'); CheckUserStatus exige 'activo'
        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $nombre, 'password' => Hash::make('password'), 'estado' => 'activo']
        );

        if ($user->estado !== 'activo') {
            $user->forceFill(['estado' => 'activo'])->save();
        }

        $user->syncRoles([$rol]);

        return $user;
    }

    private function matricula(Student $student, AcademicCycle $ciclo, Turno $turno): void
    {
        Inscription::firstOrCreate(
            ['student_id' => $student->id, 'academic_cycle_id' => $ciclo->id],
            [
                'turno_id' => $turno->id,
                'fecha_inscripcion' => now(),
                'monto_pagado' => 0,
                'saldo' => 0,
                'estado_pago' => 'pagado',
                'estado_matricula' => EstadoMatricula::ACTIVA->value,
                'user_create_id' => $this->autor,
            ]
        );
    }
}
