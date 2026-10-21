{{--
    The flex layout that pins the comment form to the bottom of the slide-over is
    inlined so it works even when the stylesheet served by CommentsStyleController
    is unavailable (blocked route, proxy, stale cache) — see issue #28.

    IMPORTANTE: un componente Livewire solo puede tener UN elemento raíz. El
    <style> de la ramificación va DENTRO del <div class="comments-wrap">, como
    su primer hijo — no como hermano antes de él. Tenerlo afuera rompía esa
    regla (dos raíces) y causaba el error "Livewire Entangle ... cannot be
    found on component" + que toda la sección desapareciera al interactuar.
--}}
<div class="comments-wrap"
    style="display: flex; flex-direction: column; flex: 1 1 0%; min-height: 0;"
    @if (!\Relaticle\Comments\CommentsConfig::isBroadcastingEnabled())
        wire:poll.{{ \Relaticle\Comments\CommentsConfig::getPollingInterval() }}
    @endif
    x-data="{ uploadError: null }"
    x-on:livewire-upload-error.window="uploadError = {{ Illuminate\Support\Js::from(__('comments::comments.attachments.upload_failed')) }}"
    x-on:livewire-upload-start.window="uploadError = null"
>
    <style>
        /* ── Ramificación de respuestas (estilo YouTube) ──────────────
           Mismo truco que usa YouTube: en vez de calcular alturas exactas
           con SVG o relleno, cada pieza se dibuja con ::before/::after y un
           "codo" de borde + radio, y el espacio (margin) entre el avatar del
           padre y la lista de respuestas se "puentea" con un bottom
           negativo. El final de la línea se corta pintando un pequeño
           rectángulo del color de fondo de la tarjeta (bg-white /
           dark:#151424 — el mismo fondo de reproductor-video.blade.php; si
           se usa en otra tarjeta con otro fondo, ajustar esos dos colores).

           ".replies-open" (la agrega Alpine con :class, no Blade) hace que
           el riel del padre SOLO se vea cuando las respuestas están
           desplegadas — antes se dibujaba siempre que hubiera respuestas,
           aunque estuvieran ocultas, dejando una línea suelta sin destino. */
        .thread-main { position: relative; }

        .thread-main.has-children.replies-open::after {
            content: ""; position: absolute; left: 15px; top: 32px; bottom: -16px;
            border-left: 1px solid rgb(99 102 241 / .18); pointer-events: none;
        }
        .dark .thread-main.has-children.replies-open::after {
            border-color: rgb(129 140 248 / .18);
        }

        /* Línea vertical común a las respuestas del mismo comentario */
        .thread-children {
            position: relative; margin: 16px 0 0 16px; padding-left: 28px;
            border-left: 1px solid rgb(99 102 241 / .18);
        }
        .dark .thread-children { border-color: rgb(129 140 248 / .18); }

        .thread-child { position: relative; }
        .thread-child + .thread-child { margin-top: 20px; }

        /* Codo redondeado que conecta una respuesta con el tronco */
        .thread-child::before {
            content: ""; position: absolute; top: 0; left: -29px; width: 24px; height: 24px;
            border-left: 1px solid rgb(99 102 241 / .18); border-bottom: 1px solid rgb(99 102 241 / .18);
            border-bottom-left-radius: 15px; pointer-events: none;
        }
        .dark .thread-child::before { border-color: rgb(129 140 248 / .18); }

        /* Corta la línea vertical justo en la última respuesta (no sigue más abajo) */
        .thread-child-last::after {
            content: ""; position: absolute; top: 24px; bottom: 0; left: -31px; width: 4px;
            background: #ffffff; pointer-events: none;
        }
        .dark .thread-child-last::after { background: #151424; }

        /* Separador entre comentarios principales (no entre respuestas) */
        .root-comment + .root-comment {
            border-top: 1px solid rgb(99 102 241 / .14); margin-top: 28px; padding-top: 24px;
        }
        .dark .root-comment + .root-comment { border-top-color: rgb(129 140 248 / .12); }
    </style>

    {{-- Formulario para comentar: arriba de la lista, siempre a la vista. Antes
         estaba al final, así que con muchos comentarios había que bajar hasta el
         fondo de la página solo para escribir uno. --}}
    @auth
        @can('create', \Relaticle\Comments\CommentsConfig::getCommentModel())
            <div class="mb-5 shrink-0 border-b border-gray-200 pb-5 dark:border-gray-700" style="flex-shrink: 0;">
                {{ $this->commentForm }}

                @if (!empty($attachments))
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($attachments as $index => $file)
                            <div class="flex items-center gap-1 rounded bg-gray-100 px-2 py-1 text-xs text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                <span>{{ $file->getClientOriginalName() }}</span>
                                <button type="button" wire:click="removeAttachment({{ $index }})" class="text-gray-400 hover:text-danger-500 dark:text-gray-500 dark:hover:text-danger-400">&times;</button>
                            </div>
                        @endforeach
                    </div>

                    @error('attachments.*')
                        <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                    @enderror
                    <p x-show="uploadError" x-text="uploadError" class="mt-1 text-sm text-danger-600 dark:text-danger-400"></p>
                @endif

                <div class="mt-2 flex items-center justify-between">
                    @if (\Relaticle\Comments\CommentsConfig::areAttachmentsEnabled())
                        <label class="flex cursor-pointer items-center gap-1.5 text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                            </svg>
                            {{ __('comments::comments.attachments.attach') }}
                            <input type="file" wire:model="attachments" multiple class="hidden" accept="{{ implode(',', \Relaticle\Comments\CommentsConfig::getAttachmentAllowedTypes()) }}" />
                        </label>
                    @else
                        <div></div>
                    @endif

                    <button type="button" wire:click="addComment"
                        class="inline-flex items-center rounded-lg bg-primary-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:bg-primary-500 dark:hover:bg-primary-400 dark:focus:ring-offset-gray-800"
                        wire:loading.attr="disabled" wire:target="addComment">
                        <span wire:loading.remove wire:target="addComment">{{ __('comments::comments.submit') }}</span>
                        <span wire:loading wire:target="addComment">{{ __('comments::comments.posting') }}</span>
                    </button>
                </div>
            </div>
        @endcan
    @endauth

    <div class="comments-body space-y-4" style="flex: 1 1 0%; min-height: 0; overflow-y: auto;">
    {{-- Sort toggle --}}
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ __('comments::comments.count', ['count' => $this->allCommentsCount]) }}
        </h3>
        <button wire:click="toggleSort" type="button"
            class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            {{ $sortDirection === 'asc' ? __('comments::comments.sort_oldest') : __('comments::comments.sort_newest') }}
        </button>
    </div>

    {{-- Pinned comments --}}
    @if (\Relaticle\Comments\CommentsConfig::isPinningEnabled() && $this->pinnedComments->isNotEmpty())
        <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            {{-- Header --}}
            <div class="flex items-center gap-2 border-b border-gray-100 px-4 py-2.5 dark:border-gray-700">
                <svg class="h-3.5 w-3.5 text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M6.32 2.577a49.255 49.255 0 0 1 11.36 0c1.497.174 2.57 1.46 2.57 2.93V21a.75.75 0 0 1-1.085.67L12 18.089l-7.165 3.583A.75.75 0 0 1 3.75 21V5.507c0-1.47 1.073-2.756 2.57-2.93Z" clip-rule="evenodd"/>
                </svg>
                <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('comments::comments.pinned') }}</span>
            </div>
            {{-- Pinned comment list with dividers --}}
            <div class="divide-y divide-gray-100 px-4 dark:divide-gray-700">
                @foreach ($this->pinnedComments as $comment)
                    <div class="py-4">
                        <livewire:comment-item :comment="$comment" :key="'pinned-'.$comment->id" />
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Comment list. Sin space-y-4: el espacio entre comentarios ya lo da
         la regla ".root-comment + .root-comment" del <style> de arriba
         (separador + margen), para no duplicar el espaciado. --}}
    <div>
        @foreach ($this->comments as $comment)
            <livewire:comment-item :comment="$comment" :key="'comment-'.$comment->id" />
        @endforeach
    </div>

    {{-- Load more button --}}
    @if ($this->hasMore)
        <div class="text-center">
            <button wire:click="loadMore" type="button"
                class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400">
                {{ __('comments::comments.load_more') }}
                <span wire:loading wire:target="loadMore" class="ml-1">...</span>
            </button>
        </div>
    @endif

    </div>{{-- end comments-body --}}
</div>
