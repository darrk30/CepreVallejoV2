<?php

namespace App\Filament\Alumno\Pages;

use App\Models\ExamenOrdinario;
use App\Models\Intento;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ExamenesOrdinarios extends Page
{
    protected string $view = 'filament.alumno.pages.examenes-ordinarios';
    protected static ?string $navigationLabel = 'Exámenes';
    protected static ?string $title = 'Exámenes Ordinarios';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    // ── Estado público (Livewire reactivo) ───────────────────────
    public array   $examenes      = [];
    public array   $ranking       = [];
    public array   $carreras      = [];
    public ?int    $examenId      = null;
    public ?string $examenTitulo  = null;
    public string  $carreraFiltro = '';
    public int     $myUserId      = 0;
    public string  $examSearch    = '';

    // Modal paginación
    public bool  $modalOpen    = false;
    public array $modalRanking = [];
    public int   $modalPage    = 1;
    public int   $modalPerPage = 20;
    public bool  $modalHasMore = false;

    // ── Mount ────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->myUserId = auth()->id();

        $this->examenes = ExamenOrdinario::where('estado', 'activo')
            ->withCount('respuestasCorrectas')
            ->latest()
            ->get()
            ->map(fn($e) => [
                'id'        => $e->id,
                'titulo'    => $e->titulo,
                'duracion'  => (int) $e->duracion_minutos,
                'preguntas' => $e->respuestas_correctas_count,
                'fecha'     => $e->created_at->diffForHumans(),
            ])
            ->toArray();
    }
    

    // ── Seleccionar examen ───────────────────────────────────────
    public function seleccionarExamen(int $id): void
    {
        $this->examenId      = $id;
        $this->carreraFiltro = '';
        $this->modalOpen     = false;

        $encontrado = collect($this->examenes)->firstWhere('id', $id);
        $this->examenTitulo = $encontrado['titulo'] ?? null;

        $this->cargarRanking();
        $this->cargarCarreras();
    }

    // ── Filtrar por carrera ──────────────────────────────────────
    public function filtrarCarrera(string $carrera): void
    {
        $this->carreraFiltro = $carrera;
        $this->cargarRanking();
    }

    // ── Carga ranking top 10 ─────────────────────────────────────
    private function cargarRanking(): void
    {
        if (!$this->examenId) {
            $this->ranking = [];
            return;
        }

        $this->ranking = $this->queryRanking($this->examenId, $this->carreraFiltro)
            ->limit(10)
            ->get()
            ->map(fn($r) => $this->mapearIntento($r))
            ->toArray();
    }

    // ── Carga carreras únicas ────────────────────────────────────
    private function cargarCarreras(): void
    {
        if (!$this->examenId) {
            $this->carreras = [];
            return;
        }

        $this->carreras = Intento::where('examen_ordinario_id', $this->examenId)
            ->with('carrera')
            ->get()
            ->pluck('carrera.nombre')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    // ── Modal ────────────────────────────────────────────────────
    public function abrirModal(): void
    {
        if (!$this->examenId) return;
        $this->modalPage    = 1;
        $this->modalRanking = [];
        $this->modalOpen    = true;
        $this->cargarPaginaModal();
    }

    public function cargarMasModal(): void
    {
        $this->modalPage++;
        $this->cargarPaginaModal();
    }

    private function cargarPaginaModal(): void
    {
        $offset = ($this->modalPage - 1) * $this->modalPerPage;

        $items = $this->queryRanking($this->examenId, $this->carreraFiltro)
            ->offset($offset)
            ->limit($this->modalPerPage + 1)
            ->get();

        $this->modalHasMore = $items->count() > $this->modalPerPage;

        $mapped = $items->take($this->modalPerPage)
            ->map(fn($r) => $this->mapearIntento($r))
            ->toArray();

        $this->modalRanking = $this->modalPage === 1
            ? $mapped
            : array_merge($this->modalRanking, $mapped);
    }

    // ── Query base ranking ───────────────────────────────────────
    private function queryRanking(int $examenId, string $carrera = '')
    {
        // Solo el mejor intento por alumno
        $subquery = Intento::selectRaw('MAX(id) as id')
            ->where('examen_ordinario_id', $examenId)
            ->groupBy('user_id');

        $query = Intento::whereIn('id', $subquery)
            ->with(['usuario', 'carrera'])
            ->orderByDesc('puntaje_obtenido');

        if ($carrera !== '') {
            $query->whereHas('carrera', fn($q) => $q->where('nombre', $carrera));
        }

        return $query;
    }

    private function mapearIntento($intento): array
    {
        return [
            'user_id' => $intento->user_id,
            'nombre'  => $intento->usuario->name ?? 'Alumno',
            'puntaje' => (float) $intento->puntaje_obtenido,
            'carrera' => $intento->carrera->nombre ?? 'Sin carrera',
            'es_yo'   => $intento->user_id === $this->myUserId,
        ];
    }

    public function irAIntentos(int $examenId): void
    {
        redirect()->to(MisIntentos::getUrl(['examen_id' => $examenId]));
    }
}
