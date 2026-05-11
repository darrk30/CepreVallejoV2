@push('styles')
    <link rel="stylesheet" href="{{ asset('css/virtual-classroom.css') }}">
@endpush
<x-filament-panels::page>

    <div class="cr">
        {{-- Header --}}
        <div class="cr-ph">
            <div>
                <h1 class="cr-title">Mi Aula <em>Virtual</em></h1>
                <p class="cr-subtitle">Cursos asignados por ciclo académico</p>
            </div>
            <div class="cr-badge">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-1.234" />
                </svg>
                Ciclo activo
            </div>
        </div>

        @forelse($this->cycles as $cycleName => $assignments)

            <div class="cr-cycle">
                <div class="cr-cycle-hd">
                    <div class="cr-stripe"></div>
                    <span class="cr-cname">{{ $cycleName }}</span>
                    <span class="cr-ccount">{{ count($assignments) }}
                        {{ count($assignments) === 1 ? 'curso' : 'cursos' }}</span>
                </div>

                <div class="cr-grid">
                    @foreach ($assignments as $assignment)
                        @php
                            $course = $assignment->cicloCourse->course;
                            $hasImage = !empty($course->imagen_path);
                        @endphp

                        <div class="cr-card">
                            <div class="cr-img">
                                @if ($hasImage)
                                    <img src="{{ Storage::url($course->imagen_path) }}" alt="{{ $course->nombre }}"
                                        loading="lazy">
                                @else
                                    <div class="cr-img-ph">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.4" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div class="cr-body">
                                <span class="cr-tag">{{ $course->codigo }}</span>
                                <h3 class="cr-name">{{ $course->nombre }}</h3>
                                {{-- <p class="cr-desc">Gestiona lecciones, material educativo y exámenes para este curso en
                                    el presente ciclo.</p> --}}
                                {{-- <a href="{{ route('filament.profesor.resources.teacher-course-contents.index', ['tableFilters[ciclo_course_teacher_id][value]' => $assignment->id]) }}" --}}
                                <a href="{{ \App\Filament\Profesor\Pages\ManageCourseContent::getUrl(['courseSlug' => $course->slug]) }}"
                                    class="cr-btn">
                                    Entrar al Aula
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2.2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        @empty
            <div class="cr-empty">
                <div class="cr-empty-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.3"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-1.234" />
                    </svg>
                </div>
                <h3>Sin cursos asignados</h3>
                <p>No tienes cursos asignados para ningún ciclo actualmente.</p>
            </div>
        @endforelse

    </div>
</x-filament-panels::page>
