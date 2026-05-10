{{-- @push('styles')
    <link rel="stylesheet" href="{{ asset('css/virtual-classroom.css') }}">
@endpush --}}
<x-filament-panels::page>
    {{-- <style>
        @import url('https://fonts.googleapis.com/css2?family=Handlee&family=Nunito:wght@300;400;600;700;800;900&display=swap');

        .cr {
            --c-surface: #ffffff;
            --c-surface3: #eeecfb;
            --c-border: rgba(99, 102, 241, 0.13);
            --c-border-s: rgba(99, 102, 241, 0.28);
            --c-text1: #0f0e17;
            --c-text2: #3d3b5c;
            --c-muted: #8e8bac;
            --c-accent: #5b5ef4;
            --c-accent-h: #4643d4;
            --c-accent-gl: rgba(91, 94, 244, 0.18);
            --c-tag-bg: rgba(91, 94, 244, 0.09);
            --c-tag-tx: #4643d4;
            --c-img-bg: #e4e2f8;
            --c-sh: 0 2px 10px rgba(79, 70, 229, 0.07), 0 1px 3px rgba(0, 0, 0, 0.04);
            --c-sh-h: 0 16px 40px rgba(79, 70, 229, 0.15), 0 4px 14px rgba(0, 0, 0, 0.07);
        }

        .dark .cr {
            --c-surface: #141320;
            --c-surface3: #211f38;
            --c-border: rgba(129, 140, 248, 0.13);
            --c-border-s: rgba(129, 140, 248, 0.30);
            --c-text1: #eeedf8;
            --c-text2: #aba8cc;
            --c-muted: #6a678a;
            --c-accent: #818cf8;
            --c-accent-h: #a5b4fc;
            --c-accent-gl: rgba(129, 140, 248, 0.22);
            --c-tag-bg: rgba(129, 140, 248, 0.13);
            --c-tag-tx: #a5b4fc;
            --c-img-bg: #22203a;
            --c-sh: 0 2px 14px rgba(0, 0, 0, 0.30);
            --c-sh-h: 0 20px 48px rgba(0, 0, 0, 0.45), 0 6px 18px rgba(0, 0, 0, 0.30);
        }

        .cr {
            font-family: 'Handlee', sans-serif;
            padding: 6px 0 48px;
        }

        /* ── Alerta sin matrícula ── */
        .cr-no-access {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 64px 32px;
            text-align: center;
            background: var(--c-surface);
            border: 1.5px dashed var(--c-border-s);
            border-radius: 16px;
            gap: 12px;
        }

        .cr-no-access svg {
            width: 52px;
            height: 52px;
            color: var(--c-muted);
            opacity: .35;
        }

        .cr-no-access h3 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--c-text2);
            margin: 0;
        }

        .cr-no-access p {
            font-size: 0.82rem;
            color: var(--c-muted);
            margin: 0;
            max-width: 340px;
        }

        /* ── Header ── */
        .cr-ph {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 38px;
            padding-bottom: 22px;
            border-bottom: 1px solid var(--c-border);
            flex-wrap: wrap;
            gap: 14px;
        }

        .cr-title {
            font-family: 'Handlee', sans-serif;
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--c-text1) !important;
            letter-spacing: -0.03em;
            line-height: 1;
            margin: 0;
        }

        .cr-title em {
            color: var(--c-accent) !important;
            font-style: normal;
        }

        .cr-subtitle {
            font-size: 0.82rem;
            color: var(--c-muted) !important;
            margin: 5px 0 0;
        }

        .cr-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--c-tag-bg) !important;
            color: var(--c-tag-tx) !important;
            font-size: 0.73rem;
            font-weight: 500;
            padding: 6px 14px;
            border-radius: 100px;
            border: 1px solid var(--c-border-s);
            white-space: nowrap;
        }

        .cr-badge svg {
            width: 13px;
            height: 13px;
        }

        /* ── Ciclo ── */
        .cr-cycle {
            margin-bottom: 42px;
        }

        .cr-cycle-hd {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .cr-stripe {
            width: 3px;
            height: 24px;
            border-radius: 4px;
            background: var(--c-accent) !important;
            flex-shrink: 0;
        }

        .cr-cname {
            font-family: 'Handlee', sans-serif;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: var(--c-accent) !important;
        }

        .cr-ccount {
            margin-left: auto;
            font-size: 0.72rem;
            color: var(--c-muted) !important;
        }

        /* ── Grid ── */
        .cr-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 18px;
        }

        /* ── Card ── */
        .cr-card {
            background: var(--c-surface) !important;
            border: 1px solid var(--c-border) !important;
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: var(--c-sh);
            transition: transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.22s ease, border-color 0.2s ease;
            position: relative;
        }

        .cr-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--c-sh-h);
            border-color: var(--c-border-s) !important;
        }

        .cr-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--c-accent) 0%, transparent 100%);
            transform: scaleX(0);
            transform-origin: left;
            opacity: 0;
            transition: opacity 0.25s ease, transform 0.3s ease;
            z-index: 1;
        }

        .cr-card:hover::before {
            opacity: 1;
            transform: scaleX(1);
        }

        /* ── Imagen ── */
        .cr-img {
            height: 152px;
            overflow: hidden;
            background: var(--c-img-bg) !important;
            flex-shrink: 0;
        }

        .cr-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }

        .cr-card:hover .cr-img img {
            transform: scale(1.05);
        }

        .cr-img-ph {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--c-surface3) !important;
        }

        .cr-img-ph svg {
            width: 38px;
            height: 38px;
            color: var(--c-accent) !important;
            opacity: 0.28;
        }

        /* ── Card body ── */
        .cr-body {
            padding: 18px 20px 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
            background: var(--c-surface) !important;
        }

        .cr-tag {
            display: inline-flex;
            align-items: center;
            background: var(--c-tag-bg) !important;
            color: var(--c-tag-tx) !important;
            font-size: 0.63rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 3px 9px;
            border-radius: 100px;
            margin-bottom: 10px;
            width: fit-content;
        }

        .cr-name {
            font-family: 'Handlee', sans-serif;
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--c-text1) !important;
            margin: 0 0 8px;
            line-height: 1.28;
        }

        .cr-desc {
            font-size: 0.79rem;
            color: var(--c-text2) !important;
            line-height: 1.55;
            flex: 1;
            margin-bottom: 18px;
        }

        /* ── Botón ── */
        .cr-btn {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--c-accent) !important;
            color: #ffffff !important;
            padding: 10px 18px;
            border-radius: 10px;
            text-decoration: none !important;
            font-size: 0.81rem;
            font-weight: 500;
            font-family: 'Handlee', sans-serif;
            transition: background 0.18s ease, transform 0.15s ease, box-shadow 0.18s ease;
            border: none !important;
            cursor: pointer;
            box-shadow: 0 2px 10px var(--c-accent-gl);
        }

        .cr-btn:hover {
            background: var(--c-accent-h) !important;
            color: #fff !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px var(--c-accent-gl);
            text-decoration: none !important;
        }

        .cr-btn svg {
            width: 13px;
            height: 13px;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }

        .cr-btn:hover svg {
            transform: translateX(2px);
        }

        @media (max-width:600px) {
            .cr-ph {
                flex-direction: column;
                align-items: flex-start;
            }

            .cr-grid {
                grid-template-columns: 1fr;
            }

            .cr-title {
                font-size: 1.45rem;
            }
        }
    </style> --}}

    <div class="cr">

        {{-- Header --}}
        <div class="cr-ph">
            <div>
                <h1 class="cr-title">Mi Aula <em>Virtual</em></h1>
                <p class="cr-subtitle">Tus cursos del ciclo académico activo</p>
            </div>
            @if ($this->inscripcion)
                <div class="cr-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-1.234" />
                    </svg>
                    {{ $this->inscripcion->academicCycle->nombre }}
                </div>
            @endif
        </div>

        {{-- Sin matrícula activa --}}
        @if ($this->cycles->isEmpty())
            <div class="cr-no-access">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.3"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
                <h3>Sin matrícula activa</h3>
                <p>No tienes una matrícula activa para ningún ciclo. Contacta a secretaría para regularizar tu
                    situación.</p>
            </div>
        @else
            {{-- Ciclos con cursos --}}
            @foreach ($this->cycles as $cycleName => $inscriptions)
                @php
                    // Tomamos el ciclo de la primera inscripción del grupo
                    $cycleId = $inscriptions->first()->academic_cycle_id;
                    $courses = $this->getCoursesForCycle($cycleId);
                @endphp

                @if ($courses->isEmpty())
                    @continue
                @endif

                <div class="cr-cycle">
                    <div class="cr-cycle-hd">
                        <div class="cr-stripe"></div>
                        <span class="cr-cname">{{ $cycleName }}</span>
                        <span class="cr-ccount">{{ $courses->count() }}
                            {{ $courses->count() === 1 ? 'curso' : 'cursos' }}</span>
                    </div>

                    <div class="cr-grid">
                        @foreach ($courses as $cicloCourse)
                            @php
                                $course = $cicloCourse->course;
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
                                    @if ($course->descripcion)
                                        <p class="cr-desc">{{ Str::limit($course->descripcion, 80) }}</p>
                                    @endif

                                    {{-- Enlace al contenido del curso (vista solo lectura para alumno) --}}
                                    <a href="{{ \App\Filament\Profesor\Pages\ManageCourseContent::getUrl(['courseSlug' => $course->slug]) }}"
                                    {{-- <a href="#" --}}
                                        class="cr-btn">
                                        Ver curso
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
            @endforeach
        @endif

    </div>
</x-filament-panels::page>
