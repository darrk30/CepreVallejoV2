<?php

namespace App\Filament\Profesor\Pages;

use App\Models\Exam;
use App\Models\ExamAttempt;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Illuminate\Support\Facades\Auth;

class ExamResults extends Page implements HasActions
{
    use InteractsWithActions;

    protected string $view = 'filament.profesor.pages.exam-results';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'exams/{examId}/results';

    public int $examId;
    public ?Exam $exam = null;

    // Panel de detalle del intento seleccionado
    public ?int $selectedAttemptId = null;

    public static function canAccess(): bool
    {
        return Auth::user()->can('create_exam');
    }

    public function mount(int $examId): void
    {
        // abort_unless(Auth::user()->can('view_exam_results'), 403);

        $this->examId = $examId;

        $this->exam = Exam::with([
            'questions.options',
            'detail.content.cicloCourseTeacher.cicloCourse.course',
        ])->findOrFail($examId);

        // Verificar que el examen pertenece a un curso del profesor autenticado
        $teacherId           = Auth::user()?->teacher?->id;
        $examTeacherId       = $this->exam->detail
            ?->content
            ?->cicloCourseTeacher
            ?->teacher_id;

        if (!$teacherId || $teacherId !== $examTeacherId) {
            abort(403);
        }
    }

    // ── Breadcrumb ───────────────────────────────────────────
    public function getBreadcrumbs(): array
    {
        $course     = $this->exam->detail?->content?->cicloCourseTeacher?->cicloCourse?->course;
        $courseSlug = $course?->slug;
        $courseName = $course?->nombre ?? 'Curso';

        return [
            VirtualClassroom::getUrl()                                             => 'Mi Aula Virtual',
            $courseSlug
                ? ManageCourseContent::getUrl(['courseSlug' => $courseSlug])
                : '#'                                                              => $courseName,
            '#'                                                                    => 'Resultados: ' . $this->exam->titulo,
        ];
    }

    public function getTitle(): string
    {
        return 'Resultados del Examen';
    }

    public function getHeading(): string
    {
        return '';
    }

    // ── Intentos finalizados ─────────────────────────────────
    public function getAttemptsProperty()
    {
        return ExamAttempt::where('exam_id', $this->examId)
            ->where('estado', 'finalizado')
            ->with(['student'])
            ->orderByDesc('puntaje_obtenido')
            ->get()
            ->map(function ($attempt) {
                $duracion = null;
                if ($attempt->fecha_inicio && $attempt->fecha_fin) {
                    $seconds  = $attempt->fecha_inicio->diffInSeconds($attempt->fecha_fin);
                    $duracion = gmdate($seconds >= 3600 ? 'H:i:s' : 'i:s', $seconds);
                }

                $aprobado = $attempt->puntaje_obtenido >= $this->exam->puntaje_minimo;

                return [
                    'id'             => $attempt->id,
                    'student_name'   => $attempt->student?->name ?? 'Desconocido',
                    'student_email'  => $attempt->student?->email ?? '—',
                    'student_avatar' => $attempt->student?->name
                        ? strtoupper(substr($attempt->student->name, 0, 1))
                        : '?',
                    'puntaje'        => $attempt->puntaje_obtenido,
                    'aprobado'       => $aprobado,
                    'duracion'       => $duracion,
                    'fecha_fin'      => $attempt->fecha_fin?->format('d/m/Y H:i'),
                    'respuestas'     => $attempt->respuestas_enviadas ?? [],
                ];
            });
    }

    // ── Intento seleccionado para el panel de detalle ────────
    public function getSelectedAttemptProperty(): ?array
    {
        if (!$this->selectedAttemptId) {
            return null;
        }

        $attempt = ExamAttempt::where('exam_id', $this->examId)
            ->where('estado', 'finalizado')
            ->with('student')
            ->find($this->selectedAttemptId);

        if (!$attempt) {
            return null;
        }

        $respuestas = $attempt->respuestas_enviadas ?? [];
        $questions  = $this->exam->questions;

        $detalle = $questions->map(function ($q, $index) use ($respuestas) {
            $respondidaId = $respuestas[$q->id] ?? null;
            $correcta     = $q->options->firstWhere('es_correcta', true);
            $respondida   = $respondidaId ? $q->options->firstWhere('id', $respondidaId) : null;
            $esCorrecta   = $respondida && $correcta && $respondida->id === $correcta->id;
            $sinRespuesta = !$respondidaId;

            return [
                'numero'        => $index + 1,
                'pregunta'      => $q->texto_pregunta,
                'puntos'        => $q->puntos,
                'imagen_path'   => $q->imagen_path,
                'es_correcta'   => $esCorrecta,
                'sin_respuesta' => $sinRespuesta,
                'correcta'      => $correcta,
                'respondida'    => $respondida,
                'options'       => $q->options,
            ];
        })->values()->toArray();

        $duracion = null;
        if ($attempt->fecha_inicio && $attempt->fecha_fin) {
            $seconds  = $attempt->fecha_inicio->diffInSeconds($attempt->fecha_fin);
            $duracion = gmdate($seconds >= 3600 ? 'H:i:s' : 'i:s', $seconds);
        }

        $aprobado = $attempt->puntaje_obtenido >= $this->exam->puntaje_minimo;

        return [
            'id'           => $attempt->id,
            'student_name' => $attempt->student?->name ?? 'Desconocido',
            'puntaje'      => $attempt->puntaje_obtenido,
            'aprobado'     => $aprobado,
            'duracion'     => $duracion,
            'fecha_fin'    => $attempt->fecha_fin?->format('d/m/Y H:i'),
            'detalle'      => $detalle,
        ];
    }

    // ── Acción: seleccionar intento ──────────────────────────
    public function selectAttempt(int $attemptId): void
    {
        $this->selectedAttemptId = ($this->selectedAttemptId === $attemptId) ? null : $attemptId;
    }

    public function clearSelection(): void
    {
        $this->selectedAttemptId = null;
    }

    // ── Estadísticas globales ────────────────────────────────
    public function getStatsProperty(): array
    {
        $attempts = $this->attempts;

        if ($attempts->isEmpty()) {
            return [
                'total'        => 0,
                'aprobados'    => 0,
                'desaprobados' => 0,
                'promedio'     => '—',
                'max'          => '—',
                'min'          => '—',
            ];
        }

        $puntajes = $attempts->pluck('puntaje');

        return [
            'total'        => $attempts->count(),
            'aprobados'    => $attempts->where('aprobado', true)->count(),
            'desaprobados' => $attempts->where('aprobado', false)->count(),
            'promedio'     => number_format($puntajes->avg(), 1),
            'max'          => number_format($puntajes->max(), 1),
            'min'          => number_format($puntajes->min(), 1),
        ];
    }
}
