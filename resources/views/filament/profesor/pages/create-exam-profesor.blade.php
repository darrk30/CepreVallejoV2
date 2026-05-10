@push('styles')
    <link rel="stylesheet" href="{{ asset('css/create-exam-profesor.css') }}">
@endpush
<x-filament-panels::page>
    <div class="exam-create-root">

        {{-- ── Banner de contexto ── --}}
        <div class="ec-context-banner">
            <div class="ec-context-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                </svg>
            </div>
            <div class="ec-context-info">
                <p class="ec-context-title">
                    {{ $this->existingExam ? 'Editando examen: ' . $this->existingExam->titulo : 'Nuevo examen' }}
                </p>
                <div class="ec-context-meta">
                    @if ($this->detail?->content?->cicloCourseTeacher?->cicloCourse?->course?->nombre)
                        <span class="ec-pill ec-pill-blue">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" style="width:10px;height:10px">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-1.234" />
                            </svg>
                            {{ $this->detail->content->cicloCourseTeacher->cicloCourse->course->nombre }}
                        </span>
                    @endif
                    @if ($this->detail?->content?->titulo)
                        <span class="ec-pill ec-pill-green">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" style="width:10px;height:10px">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                            </svg>
                            {{ $this->detail->content->titulo }}
                        </span>
                    @endif
                    @if ($this->detail?->titulo)
                        <span class="ec-pill ec-pill-amber">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" style="width:10px;height:10px">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            {{ $this->detail->titulo }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Formulario ── --}}
        <div class="ec-form-wrap">
            <form wire:submit="save">
                {{ $this->form }}

                {{-- Footer sticky dentro del card ─────────── --}}
                <div class="ec-footer">
                    {{-- Cancelar --}}
                    <button type="button" class="ec-btn ec-btn-cancel" id="btn-cancel" wire:click="cancel"
                        onclick="startLoading('btn-cancel')">
                        <span class="ec-spinner"></span>
                        <span class="ec-btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" style="width:14px;height:14px">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </span>
                        <span class="ec-btn-label">Cancelar</span>
                    </button>

                    <span class="ec-footer-info" id="footer-status">
                        {{ $this->existingExam ? 'Los cambios se guardarán sobre el examen existente.' : 'El examen se creará vinculado al tema seleccionado.' }}
                    </span>

                    <div style="display:flex;gap:10px;">
                        {{-- Guardar como borrador --}}
                        <button type="button" class="ec-btn ec-btn-save-draft" id="btn-draft" onclick="saveDraft()">
                            <span class="ec-spinner"></span>
                            <span class="ec-btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" style="width:14px;height:14px">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                                </svg>
                            </span>
                            <span class="ec-btn-label">Guardar borrador</span>
                        </button>

                        {{-- Guardar y publicar --}}
                        <button type="button" class="ec-btn ec-btn-save" id="btn-publish" onclick="savePublish()">
                            <span class="ec-spinner"></span>
                            <span class="ec-btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2.5" stroke="currentColor" style="width:14px;height:14px">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </span>
                            <span class="ec-btn-label">
                                {{ $this->existingExam ? 'Actualizar y publicar' : 'Crear y publicar' }}
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <x-filament-actions::modals />

    <script>
        function startLoading(btnId) {
            const btn = document.getElementById(btnId);
            if (btn) btn.classList.add('loading');
            // Deshabilitar todos para evitar doble clic
            ['btn-cancel', 'btn-draft', 'btn-publish'].forEach(id => {
                const b = document.getElementById(id);
                if (b && b !== btn) b.disabled = true;
            });
            document.getElementById('footer-status').textContent = 'Procesando...';
        }

        function saveDraft() {
            startLoading('btn-draft');
            @this.set('data.estado', 'borrador').then(() => {
                @this.save();
            });
        }

        function savePublish() {
            startLoading('btn-publish');
            @this.set('data.estado', 'activo').then(() => {
                @this.save();
            });
        }

        // Resetear botones si Livewire responde con error de validación
        document.addEventListener('livewire:updated', () => {
            ['btn-cancel', 'btn-draft', 'btn-publish'].forEach(id => {
                const b = document.getElementById(id);
                if (b) {
                    b.classList.remove('loading');
                    b.disabled = false;
                }
            });
            const status = document.getElementById('footer-status');
            if (status && status.textContent === 'Procesando...') {
                status.textContent =
                    '{{ $this->existingExam ? 'Los cambios se guardarán sobre el examen existente.' : 'El examen se creará vinculado al tema seleccionado.' }}';
            }
        });
    </script>
</x-filament-panels::page>
