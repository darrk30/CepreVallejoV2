<x-filament-panels::page>
    <div class="co-wrap">
        @forelse ($this->coursesByCycle as $cycleName => $cicloCourses)
            <div class="co-cycle">
                <h2 class="co-cycle-title">{{ $cycleName }}</h2>

                <div class="co-grid">
                    @foreach ($cicloCourses as $cicloCourse)
                        <div class="co-card">
                            <h3 class="co-card-title">{{ $cicloCourse->course->nombre ?? 'Curso' }}</h3>

                            @if ($cicloCourse->cicloCourseTeachers->isEmpty())
                                <p class="co-empty">Sin profesor asignado todavía.</p>
                            @else
                                <ul class="co-list">
                                    @foreach ($cicloCourse->cicloCourseTeachers as $assignment)
                                        <li class="co-row">
                                            <div class="co-row-info">
                                                <p class="co-teacher-name">
                                                    {{ $assignment->teacher?->user?->name ?? 'Profesor sin datos' }}
                                                </p>
                                                <p class="co-meta">
                                                    Turno {{ $assignment->turno?->nombre ?? '—' }}
                                                    · {{ $assignment->contents_count }} sección{{ $assignment->contents_count !== 1 ? 'es' : '' }}
                                                </p>
                                            </div>

                                            <a href="{{ \App\Filament\Pages\ManageCourseContentAdmin::getUrl(['assignmentId' => $assignment->id]) }}"
                                                class="co-btn">
                                                Gestionar
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="co-card co-card-center">
                <p class="co-empty">No hay cursos en ciclos activos.</p>
            </div>
        @endforelse
    </div>

    <style>
        .co-wrap { display: flex; flex-direction: column; gap: 2rem; }
        .co-cycle-title { font-size: 0.95rem; font-weight: 800; color: #0b0d17; margin-bottom: 0.75rem; }
        .dark .co-cycle-title { color: #f4f4f8; }

        .co-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; }

        .co-card { border: 1px solid #e5e7eb; border-radius: 0.9rem; background: #fff; padding: 1rem; }
        .dark .co-card { border-color: rgba(255,255,255,0.1); background: #18181f; }
        .co-card-center { text-align: center; }

        .co-card-title { font-size: 0.85rem; font-weight: 800; color: #0b0d17; margin: 0; }
        .dark .co-card-title { color: #f4f4f8; }

        .co-empty { margin-top: 0.5rem; font-size: 0.75rem; color: #6b7280; }
        .dark .co-empty { color: #9ca3af; }

        .co-list { margin-top: 0.75rem; display: flex; flex-direction: column; gap: 0.5rem; }

        .co-row {
            display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;
            border-radius: 0.6rem; background: #f9fafb; padding: 0.55rem 0.75rem;
        }
        .dark .co-row { background: rgba(255,255,255,0.05); }

        .co-row-info { min-width: 0; }
        .co-teacher-name {
            font-size: 0.75rem; font-weight: 700; color: #0b0d17; margin: 0;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .dark .co-teacher-name { color: #f4f4f8; }

        .co-meta { font-size: 0.7rem; color: #6b7280; margin: 0.1rem 0 0; }
        .dark .co-meta { color: #9ca3af; }

        .co-btn {
            flex-shrink: 0; border-radius: 0.5rem; background: #46449e; color: #fff;
            padding: 0.4rem 0.75rem; font-size: 0.7rem; font-weight: 700;
            text-decoration: none; transition: background 0.15s;
        }
        .co-btn:hover { background: #37356f; }
    </style>
</x-filament-panels::page>
