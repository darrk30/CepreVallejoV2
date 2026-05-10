@push('styles')
    <link rel="stylesheet" href="{{ asset('css/managerCourseContent.css') }}">
@endpush
<x-filament-panels::page>
    <div class="aula">

        {{-- BANNER --}}
        @if ($this->assignment)
            <div class="aula-banner">
                <div class="aula-banner-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-1.234" />
                    </svg>
                </div>
                <div class="aula-banner-info">
                    <h2>{{ $this->assignment->cicloCourse->course->nombre }}</h2>
                    <p>{{ $this->assignment->cicloCourse->academicCycle->nombre ?? 'Ciclo activo' }} · Gestión de
                        contenido</p>
                </div>
            </div>
        @endif

        {{-- TOOLBAR --}}
        <div class="aula-toolbar">
            <span class="aula-count">
                {{ $this->sections->count() }} {{ $this->sections->count() === 1 ? 'sección' : 'secciones' }}
            </span>
            @can('order_section')
                <div style="display:flex;gap:8px;align-items:center;">
                    <button class="btn-sort" id="sortSectionsBtn" onclick="toggleSectionSort()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5" />
                        </svg>
                        <span id="sortSectionsBtnText">Ordenar secciones</span>
                    </button>
                    <button class="btn-confirm" id="confirmSectionsBtn" onclick="confirmSectionSort()"
                        style="display:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Guardar orden
                    </button>
                </div>
            @endcan
        </div>

        {{-- SECCIONES --}}
        @if ($this->sections->isEmpty())
            <div class="empty-global">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.3"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <h3>Sin secciones todavía</h3>
                <p>Crea la primera sección con el botón superior.</p>
            </div>
        @else
            <div id="sectionsContainer">
                @foreach ($this->sections as $si => $section)
                    <div class="sec-card" data-id="{{ $section->id }}">

                        {{-- HEAD --}}
                        <div class="sec-head">

                            <div class="drag-handle sec-handle-global" id="secHandle-{{ $section->id }}"
                                title="Arrastrar para reordenar">
                                <span></span><span></span><span></span>
                            </div>

                            <div class="sec-badge" id="secBadge-{{ $section->id }}">{{ $si + 1 }}</div>
                            <span class="sec-title">{{ $section->titulo }}</span>
                            <span class="sec-meta">{{ $section->details->count() }}
                                tema{{ $section->details->count() !== 1 ? 's' : '' }}</span>

                            @php $activeExams = $section->details->filter(fn($d) => isset($d->exam) && $d->exam && $d->exam->estado === 'activo')->count(); @endphp
                            @if ($activeExams > 0)
                                <span class="sec-exam-badge">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                    </svg>
                                    {{ $activeExams }} examen{{ $activeExams !== 1 ? 'es' : '' }}
                                </span>
                            @endif

                            {{-- ── ACCIONES DESKTOP ── --}}
                            <div class="aula-actions-desktop">
                                @can('order_topic')
                                    {{-- Ordenar temas --}}
                                    <button class="icon-btn btn-sort-icon" id="sortDetailsBtn-{{ $section->id }}"
                                        data-tip="Ordenar temas" onclick="toggleDetailSort({{ $section->id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                                        </svg>
                                    </button>

                                    {{-- Confirmar orden temas --}}
                                    <button class="icon-btn" id="confirmDetailsBtn-{{ $section->id }}"
                                        data-tip="Guardar orden" onclick="confirmDetailSort({{ $section->id }})"
                                        style="display:none; color:var(--green); background:var(--green-bg); border-color:var(--green-bd);">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    </button>
                                @endcan
                                @can('create_topic')
                                    <div class="sec-divider"></div>

                                    {{-- Nuevo tema --}}
                                    <button class="icon-btn btn-add" data-tip="Nuevo tema"
                                        onclick="window.Livewire.dispatch('open-modal', { id: 'createSubtopic-{{ $section->id }}' })"
                                        wire:click="mountAction('createSubtopic', { section_id: {{ $section->id }} })">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </button>
                                @endcan
                                @can('update_section')
                                    <div class="sec-divider"></div>
                                    {{-- Editar sección --}}
                                    <button class="icon-btn btn-edit" data-tip="Editar sección"
                                        wire:click="mountAction('editSection', { section_id: {{ $section->id }} })">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                        </svg>
                                    </button>
                                @endcan
                                @can('delete_section')
                                    {{-- Eliminar sección --}}
                                    <button class="icon-btn btn-del" data-tip="Eliminar sección"
                                        wire:click="mountAction('deleteSection', { section_id: {{ $section->id }} })">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                @endcan
                            </div>

                            {{-- ── DROPDOWN MÓVIL ── --}}
                            <div class="aula-actions-mobile">
                                <button class="btn-more"
                                    onclick="toggleDropdown('sec-drop-{{ $section->id }}', event)"
                                    aria-label="Más acciones">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="5" cy="12" r="1.5" />
                                        <circle cx="12" cy="12" r="1.5" />
                                        <circle cx="19" cy="12" r="1.5" />
                                    </svg>
                                </button>
                                <div class="dropdown-menu" id="sec-drop-{{ $section->id }}">
                                    @can('order_topic')
                                        {{-- Ordenar temas / Cancelar --}}
                                        <button class="dropdown-item item-sort"
                                            id="sortDetailsMobile-{{ $section->id }}"
                                            onclick="closeAllDropdowns(); toggleDetailSort({{ $section->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                                            </svg>
                                            <span id="sortDetailsMobileText-{{ $section->id }}">Ordenar temas</span>
                                        </button>

                                        {{-- Confirmar orden (solo visible si sort activo) --}}
                                        <button class="dropdown-item item-confirm"
                                            id="confirmDetailsMobile-{{ $section->id }}"
                                            onclick="closeAllDropdowns(); confirmDetailSort({{ $section->id }})"
                                            style="display:none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                            Guardar orden
                                        </button>
                                    @endcan
                                    @can('create_topic')
                                        <div class="dropdown-sep"></div>
                                        {{-- Nuevo tema --}}
                                        <button class="dropdown-item item-add"
                                            onclick="closeAllDropdowns(); window.Livewire.dispatch('open-modal', { id: 'createSubtopic-{{ $section->id }}' })"
                                            wire:click="mountAction('createSubtopic', { section_id: {{ $section->id }} })">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            Nuevo tema
                                        </button>
                                    @endcan
                                    @can('update_section')
                                        <div class="dropdown-sep"></div>
                                        {{-- Editar --}}
                                        <button class="dropdown-item item-edit" onclick="closeAllDropdowns()"
                                            wire:click="mountAction('editSection', { section_id: {{ $section->id }} })">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                            </svg>
                                            Editar sección
                                        </button>
                                    @endcan
                                    @can('delete_section')
                                        {{-- Eliminar --}}
                                        <button class="dropdown-item item-del" onclick="closeAllDropdowns()"
                                            wire:click="mountAction('deleteSection', { section_id: {{ $section->id }} })">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                            Eliminar sección
                                        </button>
                                    @endcan
                                </div>
                            </div>

                        </div>{{-- /sec-head --}}

                        {{-- DESCRIPCIÓN de sección --}}
                        @if (!empty($section->descripcion))
                            <div class="sec-desc">{{ $section->descripcion }}</div>
                        @endif

                        {{-- SORT BAR --}}
                        <div class="sort-bar" id="sortBar-{{ $section->id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                            </svg>
                            <span>Arrastra los temas para reordenar. Pulsa <strong>Guardar orden</strong> cuando
                                termines.</span>
                        </div>

                        {{-- DETALLES --}}
                        <div class="details-list" id="detailsList-{{ $section->id }}">
                            @forelse($section->details as $di => $detail)
                                <div class="detail-row" data-id="{{ $detail->id }}"
                                    data-section="{{ $section->id }}" x-data="{ openVideo: false }">

                                    <div class="drag-handle sm detail-handle-{{ $section->id }}"
                                        title="Arrastrar tema">
                                        <span></span><span></span><span></span>
                                    </div>

                                    <div class="detail-num">{{ $di + 1 }}</div>

                                    <div class="detail-content">
                                        <div class="detail-title">{{ $detail->titulo }}</div>
                                        @if (!empty($detail->descripcion))
                                            <div class="detail-desc">{{ $detail->descripcion }}</div>
                                        @endif
                                        <div class="detail-resources">

                                            @if ($detail->archivo_path)
                                                <a href="{{ Storage::url($detail->archivo_path) }}"
                                                    download="{{ basename($detail->archivo_path) }}" target="_blank"
                                                    class="res-file"
                                                    title="Descargar {{ basename($detail->archivo_path) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                                    </svg>
                                                    {{ basename($detail->archivo_path) }}
                                                </a>
                                            @endif

                                            @if ($detail->url_video)
                                                <button class="res-video" @click="openVideo = !openVideo">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                                                    </svg>
                                                    <span x-text="openVideo ? 'Ocultar video' : 'Ver video'"></span>
                                                </button>
                                            @endif

                                            @php $exam = $detail->exam ?? null; @endphp
                                            @if ($exam && $exam->estado === 'activo')
                                                <a href="{{ \App\Filament\Profesor\Pages\TakeExam::getUrl(['examId' => $exam->id]) }}"
                                                    class="res-exam" title="{{ $exam->titulo }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                                    </svg>
                                                    {{ $exam->titulo }}
                                                </a>
                                            @endif
                                        </div>

                                        @if ($detail->url_video)
                                            <template x-if="openVideo">
                                                <div class="video-wrap">
                                                    <iframe
                                                        x-bind:src="buildEmbedUrl('{{ addslashes($detail->url_video) }}')"
                                                        allowfullscreen
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                        referrerpolicy="strict-origin-when-cross-origin">
                                                    </iframe>
                                                </div>
                                            </template>
                                        @endif
                                    </div>

                                    {{-- ── ACCIONES DETALLE DESKTOP ── --}}
                                    <div class="detail-actions aula-actions-desktop">
                                        {{-- Examen: crea si no existe, edita si ya existe --}}
                                        @php $detailExam = $detail->exam ?? null; @endphp
                                        @can($detailExam ? 'update_exam' : 'create_exam')
                                            @if ($detailExam)
                                                {{-- LÓGICA: Mostrar botón si hay más de 0 intentos finalizados --}}
                                                @php
                                                    $hasResults = \App\Models\ExamAttempt::where(
                                                        'exam_id',
                                                        $detailExam->id,
                                                    )
                                                        ->where('estado', 'finalizado')
                                                        ->exists();
                                                @endphp

                                                @if ($hasResults)
                                                    <a href="{{ \App\Filament\Profesor\Pages\ExamResults::getUrl(['examId' => $detailExam->id]) }}"
                                                        class="icon-btn btn-results" data-tip="Ver resultados de alumnos"
                                                        style="color: var(--blue); background: var(--blue-bg); border-color: var(--blue-bd);">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                                        </svg>
                                                    </a>
                                                @endif
                                            @endif


                                            <button class="icon-btn {{ $detailExam ? 'btn-edit' : 'btn-add' }}"
                                                data-tip="{{ $detailExam ? 'Editar examen' : 'Crear examen' }}"
                                                wire:click="mountAction('manageExam', { subtopic_id: {{ $detail->id }} })">
                                                @if ($detailExam)
                                                    {{-- Ícono lápiz sobre documento --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                                    </svg>
                                                @else
                                                    {{-- Ícono documento + --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                    </svg>
                                                @endif
                                            </button>
                                        @endcan
                                        @can('update_topic')
                                            <button class="icon-btn btn-edit" data-tip="Editar tema"
                                                wire:click="mountAction('editSubtopic', { subtopic_id: {{ $detail->id }} })">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                                </svg>
                                            </button>
                                        @endcan
                                        @can('delete_topic')
                                            <button class="icon-btn btn-del" data-tip="Eliminar tema"
                                                wire:click="mountAction('deleteSubtopic', { subtopic_id: {{ $detail->id }} })">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        @endcan
                                    </div>

                                    {{-- ── DROPDOWN DETALLE MÓVIL ── --}}
                                    <div class="aula-actions-mobile" style="padding-top:1px;">
                                        <button class="btn-more"
                                            onclick="toggleDropdown('det-drop-{{ $detail->id }}', event)"
                                            aria-label="Más acciones">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <circle cx="5" cy="12" r="1.5" />
                                                <circle cx="12" cy="12" r="1.5" />
                                                <circle cx="19" cy="12" r="1.5" />
                                            </svg>
                                        </button>
                                        <div class="dropdown-menu" id="det-drop-{{ $detail->id }}">
                                            {{-- INICIO: Botón de Resultados para Móvil --}}
                                            @php $detailExam = $detail->exam ?? null; @endphp
                                            @can($detailExam ? 'update_exam' : 'create_exam')
                                                @if ($detailExam)
                                                    @php
                                                        $hasResults = \App\Models\ExamAttempt::where(
                                                            'exam_id',
                                                            $detailExam->id,
                                                        )
                                                            ->where('estado', 'finalizado')
                                                            ->exists();
                                                    @endphp

                                                    @if ($hasResults)
                                                        <a href="{{ \App\Filament\Profesor\Pages\ExamResults::getUrl(['examId' => $detailExam->id]) }}"
                                                            class="dropdown-item" style="color: var(--blue);">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="2"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                                            </svg>
                                                            Ver resultados
                                                        </a>
                                                        <div class="dropdown-sep"></div>
                                                    @endif
                                                @endif
                                                {{-- 2. OPCIÓN: CREAR O EDITAR EXAMEN --}}
                                                <button class="dropdown-item {{ $detailExam ? 'item-edit' : 'item-add' }}"
                                                    onclick="closeAllDropdowns()"
                                                    wire:click="mountAction('manageExam', { subtopic_id: {{ $detail->id }} })">
                                                    @if ($detailExam)
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                                        </svg>
                                                        Editar examen
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                        </svg>
                                                        Crear examen
                                                    @endif
                                                </button>
                                            @endcan

                                            <div class="dropdown-sep"></div>
                                            {{-- FIN: Botón de Resultados --}}
                                            @can('update_topic')
                                                <button class="dropdown-item item-edit" onclick="closeAllDropdowns()"
                                                    wire:click="mountAction('editSubtopic', { subtopic_id: {{ $detail->id }} })">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                                    </svg>
                                                    Editar tema
                                                </button>
                                            @endcan
                                            @can('delete_topic')
                                                <button class="dropdown-item item-del" onclick="closeAllDropdowns()"
                                                    wire:click="mountAction('deleteSubtopic', { subtopic_id: {{ $detail->id }} })">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                    Eliminar tema
                                                </button>
                                            @endcan
                                        </div>
                                    </div>

                                </div>
                            @empty
                                <div class="empty-section">No hay temas en esta sección aún.</div>
                            @endforelse
                        </div>

                    </div>{{-- /sec-card --}}
                @endforeach
            </div>{{-- /sectionsContainer --}}
        @endif

    </div>

    <x-filament-actions::modals />
    <script src="{{ asset('js/managerCourseContent.js') }}"></script>
</x-filament-panels::page>
