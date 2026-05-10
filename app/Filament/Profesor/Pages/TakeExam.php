<?php

namespace App\Filament\Profesor\Pages;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use Filament\Pages\Page;
use Filament\Actions\Action;
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
    public ?Exam        $exam        = null;
    public ?ExamAttempt $attempt     = null;
    public bool         $isPreview   = false;
    public bool         $showResults = false;

    public function mount(int $examId): void
    {
        $this->examId = $examId;

        $this->exam = Exam::with([
            'questions'         => fn($q) => $q->orderBy('orden')->orderBy('id'),
            'questions.options' => fn($q) => $q->orderBy('id'),
            'detail.content.cicloCourseTeacher.cicloCourse.course',
        ])->where('estado', 'activo')->findOrFail($examId);

        $userId    = Auth::id();
        $teacherId = Auth::user()?->teacher?->id;

        // ── Modo preview ──────────────────────────────────────
        $creadorId          = $this->exam->user_create_id;
        $profesorAsignadoId = $this->exam->detail?->content?->cicloCourseTeacher?->teacher_id;

        if ($userId === $creadorId || ($teacherId && $teacherId === $profesorAsignadoId)) {
            $this->isPreview = true;
            return;
        }

        // ── ¿Ya tiene intento finalizado? → resultados ────────
        $intentoFinalizado = ExamAttempt::where('exam_id', $examId)
            ->where('user_id', $userId)
            ->where('estado', 'finalizado')
            ->latest()
            ->first();

        if ($intentoFinalizado) {
            $this->attempt     = $intentoFinalizado;
            $this->showResults = true;
            return;
        }

        // ── Verificar intentos disponibles ────────────────────
        $intentosUsados = ExamAttempt::where('exam_id', $examId)
            ->where('user_id', $userId)
            ->whereIn('estado', ['finalizado', 'expirado'])
            ->count();

        if ($intentosUsados >= $this->exam->intentos_maximos) {
            $this->redirect($this->getCourseUrl());
            return;
        }

        // ── Retomar o crear intento ───────────────────────────
        $duracionTotal = $this->exam->duracion_minutos * 60;

        // Buscar intento en progreso existente
        $intentoExistente = ExamAttempt::where('exam_id', $examId)
            ->where('user_id', $userId)
            ->where('estado', 'en_progreso')
            ->first();

        if ($intentoExistente) {
            $this->attempt = $intentoExistente;

            // ✅ CALCULAR el tiempo real restante AHORA
            $tiempoReal = $this->getRemainingSeconds();

            // ✅ PERSISTIR con timer_synced_at = now() para que el próximo
            //    getRemainingSeconds() parta de un estado limpio
            ExamAttempt::withoutTimestamps(function () use ($tiempoReal) {
                ExamAttempt::where('id', $this->attempt->id)
                    ->update([
                        'segundos_restantes' => $tiempoReal,
                        'timer_synced_at'    => now(),
                    ]);
            });

            $this->attempt->segundos_restantes = $tiempoReal;
            $this->attempt->timer_synced_at    = now();
        } else {
            // Nuevo intento: crear con tiempo completo y timer_synced_at = now
            $this->attempt = ExamAttempt::create([
                'exam_id'             => $examId,
                'user_id'             => $userId,
                'estado'              => 'en_progreso',
                'fecha_inicio'        => now(),
                'respuestas_enviadas' => [],
                'puntaje_obtenido'    => 0,
                'segundos_restantes'  => $duracionTotal,
                'timer_synced_at'     => now(),
            ]);
        }

        // Verificar si ya expiró al retomar
        if ($this->getRemainingSeconds() <= 0) {
            $this->finalizarConPuntaje();
            $this->showResults = true;
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

    // ── Tiempo restante ───────────────────────────────────────
    // Calcula: segundos_guardados - segundos_transcurridos_desde_timer_synced_at
    // timer_synced_at se actualiza SOLO en syncTimer(), nunca en saveAnswer().
    // Así evitamos que updated_at o cualquier otra operación corrompa el cálculo.
    public function getRemainingSeconds(): int
    {
        if ($this->attempt === null) return 0;

        $saved   = (int) ($this->attempt->segundos_restantes ?? ($this->exam->duracion_minutos * 60));
        $refTime = $this->attempt->timer_synced_at ?? $this->attempt->created_at;
        $elapsed = (int) now()->diffInSeconds($refTime);

        return max(0, $saved - $elapsed);
    }

    // ── Guardar respuesta (NO modifica el timer) ──────────────
    #[Renderless]
    public function saveAnswer(int $questionId, ?int $optionId): void
    {
        if ($this->isPreview || $this->attempt === null) return;
        if ($this->attempt->estado !== 'en_progreso') return;

        $respuestas = $this->attempt->respuestas_enviadas ?? [];

        if ($optionId === null) {
            unset($respuestas[$questionId]);
        } else {
            $respuestas[$questionId] = $optionId;
        }

        // withoutTimestamps: no toca updated_at para no interferir
        // con el cálculo del tiempo en getRemainingSeconds()
        ExamAttempt::withoutTimestamps(function () use ($respuestas) {
            ExamAttempt::where('id', $this->attempt->id)
                ->update(['respuestas_enviadas' => json_encode($respuestas)]);
        });

        $this->attempt->respuestas_enviadas = $respuestas;
    }

    // ── Sincronizar timer desde JS ────────────────────────────
    // El JS es la fuente de verdad del tiempo.
    // Guardamos: el valor exacto que manda el JS + el momento en que se guardó.
    // Al retomar: remaining = segundos_restantes - elapsed_desde_timer_synced_at
    #[Renderless]
    public function syncTimer(int $secondsRemaining): void
    {
        if ($this->isPreview || $this->attempt === null) return;
        if ($this->attempt->estado !== 'en_progreso') return;

        $segundos = max(0, $secondsRemaining);
        $now      = now();

        // withoutTimestamps: solo actualizamos nuestros campos,
        // updated_at queda intacto para no romper ninguna otra lógica
        ExamAttempt::withoutTimestamps(function () use ($segundos, $now) {
            ExamAttempt::where('id', $this->attempt->id)
                ->update([
                    'segundos_restantes' => $segundos,
                    'timer_synced_at'    => $now,
                ]);
        });

        $this->attempt->segundos_restantes = $segundos;
        $this->attempt->timer_synced_at    = $now;

        if ($segundos <= 0) {
            $this->finalizarConPuntaje();
        }
    }

    // ── Calcular puntaje y finalizar ──────────────────────────
    private function finalizarConPuntaje(): void
    {
        if ($this->attempt === null) return;
        if (in_array($this->attempt->estado, ['finalizado', 'expirado'])) return;

        $respuestas = $this->attempt->respuestas_enviadas ?? [];

        $preguntas = Question::whereIn('id', $this->exam->questions->pluck('id'))
            ->with(['options' => fn($q) => $q->where('es_correcta', true)->select('id', 'question_id')])
            ->get()
            ->keyBy('id');

        $aciertos = 0;
        foreach ($preguntas as $questionId => $question) {
            $correctaId   = $question->options->first()?->id;
            $respondidaId = $respuestas[(string) $questionId] ?? null;
            if ($correctaId && (int) $respondidaId === (int) $correctaId) {
                $aciertos++;
            }
        }

        $totalPreguntas = $this->exam->questions->count();
        $totalPuntos    = $this->exam->questions->sum('puntos');
        $puntaje        = $totalPreguntas > 0
            ? round(($aciertos / $totalPreguntas) * $totalPuntos, 2)
            : 0;

        ExamAttempt::where('id', $this->attempt->id)
            ->update([
                'puntaje_obtenido'   => $puntaje,
                'fecha_fin'          => now(),
                'estado'             => 'finalizado',
                'segundos_restantes' => 0,
                'timer_synced_at'    => now(),
            ]);

        $this->attempt->puntaje_obtenido = $puntaje;
        $this->attempt->estado           = 'finalizado';
    }

    // ── Modales ───────────────────────────────────────────────
    public function confirmSubmitAction(): Action
    {
        return Action::make('confirmSubmit')
            ->label('Entregar examen')
            ->modalHeading('¿Entregar el examen?')
            ->modalDescription('Una vez entregado no podrás modificar tus respuestas. ¿Estás seguro?')
            ->modalSubmitActionLabel('Sí, entregar')
            ->modalCancelActionLabel('Volver al examen')
            ->modalIcon('heroicon-o-document-check')
            ->color('primary')
            ->requiresConfirmation()
            ->action(fn() => $this->submitExam());
    }

    public function confirmAbandonAction(): Action
    {
        $restantes        = $this->intentos_restantes;
        $ultimo           = $restantes <= 1;
        $restantesDespues = max(0, $restantes - 1);

        return Action::make('confirmAbandon')
            ->label('Abandonar')
            ->modalHeading($ultimo ? 'Último intento disponible' : 'Abandonar examen')
            ->modalDescription(
                $ultimo
                    ? 'Este es tu único intento. Si abandonas, se entregará con las respuestas actuales.'
                    : "Si abandonas perderás este intento. Te quedarán {$restantesDespues} intento" . ($restantesDespues !== 1 ? 's' : '') . '.'
            )
            ->modalSubmitActionLabel($ultimo ? 'Entregar y salir' : 'Sí, abandonar')
            ->modalCancelActionLabel('Seguir en el examen')
            ->modalIcon($ultimo ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-arrow-left-start-on-rectangle')
            ->color('danger')
            ->requiresConfirmation()
            ->action(fn() => $this->abandonExam());
    }

    // ── Acciones principales ──────────────────────────────────
    public function submitExam(): void
    {
        if ($this->isPreview) {
            $this->redirect($this->getCourseUrl());
            return;
        }
        $this->finalizarConPuntaje();
        $this->showResults = true;
    }

    public function abandonExam(): void
    {
        if ($this->isPreview) {
            $this->redirect($this->getCourseUrl());
            return;
        }

        $intentosUsados    = ExamAttempt::where('exam_id', $this->examId)
            ->where('user_id', Auth::id())
            ->whereIn('estado', ['finalizado', 'expirado'])
            ->count();
        $intentosRestantes = $this->exam->intentos_maximos - $intentosUsados;

        if ($intentosRestantes <= 1) {
            $this->finalizarConPuntaje();
            $this->showResults = true;
        } else {
            ExamAttempt::where('id', $this->attempt->id)
                ->update([
                    'fecha_fin'          => now(),
                    'estado'             => 'expirado',
                    'segundos_restantes' => 0,
                    'timer_synced_at'    => now(),
                ]);
            $this->redirect($this->getCourseUrl());
        }
    }

    public function expireExam(): void
    {
        if ($this->isPreview) return;
        $this->finalizarConPuntaje();
        $this->showResults = true;
    }

    public function goBackToCourse(): void
    {
        $this->redirect($this->getCourseUrl());
    }

    // ── Computed ──────────────────────────────────────────────
    public function getIntentosRestantesProperty(): int
    {
        if ($this->isPreview) return 0;
        $usados = ExamAttempt::where('exam_id', $this->examId)
            ->where('user_id', Auth::id())
            ->whereIn('estado', ['finalizado', 'expirado'])
            ->count();
        return max(0, $this->exam->intentos_maximos - $usados);
    }

    public function getResultsDataProperty(): array
    {
        if (!$this->attempt || !$this->showResults) return [];

        $respuestas = $this->attempt->respuestas_enviadas ?? [];

        $questions = $this->exam->questions()
            ->with(['options' => fn($q) => $q->orderBy('id')])
            ->orderBy('orden')->orderBy('id')
            ->get();

        $results = [];
        foreach ($questions as $qi => $question) {
            $correctaOption   = $question->options->firstWhere('es_correcta', true);
            $respondidaId     = isset($respuestas[(string) $question->id])
                ? (int) $respuestas[(string) $question->id]
                : null;
            $respondidaOption = $respondidaId
                ? $question->options->firstWhere('id', $respondidaId)
                : null;
            $esCorrecta = $correctaOption && $respondidaId === (int) $correctaOption->id;

            $results[] = [
                'numero'        => $qi + 1,
                'question'      => $question,
                'correcta'      => $correctaOption,
                'respondida'    => $respondidaOption,
                'es_correcta'   => $esCorrecta,
                'sin_respuesta' => $respondidaId === null,
            ];
        }

        return $results;
    }

    private function getCourseUrl(): string
    {
        $slug = $this->exam->detail?->content?->cicloCourseTeacher?->cicloCourse?->course?->slug;
        return $slug
            ? ManageCourseContent::getUrl(['courseSlug' => $slug])
            : VirtualClassroom::getUrl();
    }
}
