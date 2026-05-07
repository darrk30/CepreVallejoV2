<?php

namespace App\Filament\Profesor\Pages;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use Filament\Pages\Page;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Renderless;

class TakeExam extends Page implements HasActions
{
    use InteractsWithActions;

    protected string $view = 'filament.profesor.pages.take-exam';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'exams/{examId}/take';

    public int          $examId;
    public ?Exam        $exam    = null;
    public ?ExamAttempt $attempt = null;

    public function mount(int $examId): void
    {
        $this->examId = $examId;

        $this->exam = Exam::with([
            'questions'         => fn($q) => $q->orderBy('id'),
            'questions.options' => fn($q) => $q->select('id', 'question_id', 'texto_opcion', 'imagen_path'),
            'detail.content.cicloCourseTeacher.cicloCourse.course',
        ])->where('estado', 'activo')->findOrFail($examId);

        // Contar intentos ya finalizados/expirados
        $intentosUsados = ExamAttempt::where('exam_id', $examId)
            ->where('user_id', Auth::id())
            ->whereIn('estado', ['finalizado', 'expirado'])
            ->count();

        // Si ya agotó todos los intentos → redirigir
        if ($intentosUsados >= $this->exam->intentos_maximos) {
            $this->redirect($this->getCourseUrl());
            return;
        }

        // Retomar intento en progreso o crear uno nuevo
        $this->attempt = ExamAttempt::firstOrCreate(
            [
                'exam_id' => $examId,
                'user_id' => Auth::id(),
                'estado'  => 'en_progreso',
            ],
            [
                'fecha_inicio'               => now(),
                'respuestas_enviadas'        => [],
                'puntaje_obtenido'           => 0,
                'duracion_minutos_restantes' => $this->exam->duracion_minutos,
            ]
        );

        // Si el intento existía pero el tiempo ya expiró → auto-entregar
        if ($this->getRemainingSeconds() <= 0) {
            $this->finalizarConPuntaje();
            $this->redirect($this->getCourseUrl());
            return;
        }
    }

    public function getHeading(): string
    {
        return '';
    }

    public function getTitle(): string
    {
        return $this->exam?->titulo ?? 'Examen';
    }

    // ── Calcular segundos restantes ───────────────────────────
    // Usa duracion_minutos_restantes si existe (retoma),
    // de lo contrario calcula desde fecha_inicio.
    public function getRemainingSeconds(): int
    {
        if ($this->attempt === null) return 0;

        // Si guardamos minutos restantes, los usamos como base
        // y ajustamos por el tiempo transcurrido desde la última sincronización
        // (fecha_updated_at del intento)
        $savedSeconds = (int) ($this->attempt->duracion_minutos_restantes * 60);

        // Tiempo transcurrido desde que se actualizó por última vez
        $elapsed = (int) now()->diffInSeconds($this->attempt->updated_at);

        return max(0, $savedSeconds - $elapsed);
    }

    // ── Guardar respuesta individual ──────────────────────────
    #[Renderless]
    public function saveAnswer(int $questionId, ?int $optionId): void
    {
        $respuestas = $this->attempt->respuestas_enviadas ?? [];

        if ($optionId === null) {
            unset($respuestas[$questionId]);
        } else {
            $respuestas[$questionId] = $optionId;
        }

        $this->attempt->update([
            'respuestas_enviadas' => $respuestas,
        ]);
    }

    // ── Sincronizar tiempo restante (llamado periódicamente desde JS) ──
    #[Renderless]
    public function syncTimer(int $secondsRemaining): void
    {
        if ($this->attempt === null) return;

        $minutes = max(0, round($secondsRemaining / 60, 4));

        $this->attempt->update([
            'duracion_minutos_restantes' => $minutes,
        ]);

        // Si el tiempo expiró → finalizar automáticamente
        if ($secondsRemaining <= 0) {
            $this->finalizarConPuntaje();
        }
    }

    // ── Calcular puntaje y marcar como finalizado ─────────────
    private function finalizarConPuntaje(): void
    {
        // Evitar doble finalización
        if ($this->attempt->estado === 'finalizado') return;

        $respuestas = $this->attempt->respuestas_enviadas ?? [];

        $correctas = Question::whereIn('id', $this->exam->questions->pluck('id'))
            ->with(['options' => fn($q) => $q->where('es_correcta', true)->select('id', 'question_id')])
            ->get()
            ->keyBy('id');

        $aciertos = 0;

        foreach ($correctas as $questionId => $question) {
            $correctaId   = $question->options->first()?->id;
            $respondidaId = $respuestas[(string) $questionId] ?? null;

            if ($correctaId && (int) $respondidaId === (int) $correctaId) {
                $aciertos++;
            }
        }

        $totalPreguntas = $this->exam->questions->count();
        $totalPuntos    = $this->exam->questions->sum('puntos');

        $puntaje = $totalPreguntas > 0
            ? round(($aciertos / $totalPreguntas) * $totalPuntos, 2)
            : 0;

        $this->attempt->update([
            'puntaje_obtenido'           => $puntaje,
            'fecha_fin'                  => now(),
            'estado'                     => 'finalizado',
            'duracion_minutos_restantes' => 0,
        ]);
    }

    // ── Entregar examen (desde botón) ─────────────────────────
    public function submitExam(): void
    {
        $this->finalizarConPuntaje();
        $this->redirect($this->getCourseUrl());
    }

    // ── Abandonar examen ──────────────────────────────────────
    // Si solo queda 1 intento → finalizar con lo que tiene (no desperdiciar)
    // Si tiene más intentos disponibles → marcar como expirado
    public function abandonExam(): void
    {
        $intentosUsados = ExamAttempt::where('exam_id', $this->examId)
            ->where('user_id', Auth::id())
            ->whereIn('estado', ['finalizado', 'expirado'])
            ->count();

        $intentosRestantes = $this->exam->intentos_maximos - $intentosUsados;

        if ($intentosRestantes <= 1) {
            // Último intento → entregar con lo que tiene
            $this->finalizarConPuntaje();
        } else {
            // Tiene más intentos → marcar como expirado
            $this->attempt->update([
                'fecha_fin'                  => now(),
                'estado'                     => 'expirado',
                'duracion_minutos_restantes' => 0,
            ]);
        }

        $this->redirect($this->getCourseUrl());
    }

    // ── Auto-expirar desde JS (tiempo agotado) ────────────────
    public function expireExam(): void
    {
        $this->finalizarConPuntaje();
        $this->redirect($this->getCourseUrl());
    }

    // ── Datos del examen para el JS ───────────────────────────
    public function getIntentosRestantesProperty(): int
    {
        $usados = ExamAttempt::where('exam_id', $this->examId)
            ->where('user_id', Auth::id())
            ->whereIn('estado', ['finalizado', 'expirado'])
            ->count();

        return max(0, $this->exam->intentos_maximos - $usados);
    }

    // ── URL de retorno al curso ───────────────────────────────
    private function getCourseUrl(): string
    {
        $slug = $this->exam
            ->detail
            ?->content
            ?->cicloCourseTeacher
            ?->cicloCourse
            ?->course
            ?->slug;

        if ($slug) {
            return \App\Filament\Profesor\Pages\ManageCourseContent::getUrl(['courseSlug' => $slug]);
        }

        return \App\Filament\Profesor\Pages\VirtualClassroom::getUrl();
    }
}
