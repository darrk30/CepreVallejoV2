@php
    $hasReplies = $comment->relationLoaded('replies') && $comment->replies->isNotEmpty();
    $isRoot = is_null($comment->parent_id);
@endphp

{{-- Ramificación estilo YouTube: ver notas en comments.blade.php (ahí está el
     <style> con las clases thread-main/thread-children/thread-child). La
     clase "root-comment" solo va en el comentario de nivel 0: ahí es donde
     se dibuja la línea separadora entre comentarios distintos. --}}
<div id="comment-{{ $comment->id }}" class="{{ $isRoot ? 'root-comment' : '' }}" wire:key="comment-item-{{ $comment->id }}" @if ($hasReplies) x-data="{ showReplies: false }" @endif>
    {{-- "replies-open" la pone Alpine (no Blade): el riel que conecta el
         avatar con la lista de respuestas solo debe verse cuando esa lista
         está desplegada, si no queda una línea suelta sin destino cuando
         están ocultas. --}}
    <div class="thread-main {{ $hasReplies ? 'has-children' : '' }} flex gap-3" @if ($hasReplies) :class="{ 'replies-open': showReplies }" @endif>
        {{-- Avatar --}}
        <div class="shrink-0">
            @if ($comment->trashed())
                <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700"></div>
            @elseif ($comment->commenter?->getCommentAvatarUrl())
                <img src="{{ $comment->commenter->getCommentAvatarUrl() }}" alt="{{ $comment->commenter->getCommentDisplayName() }}" class="h-8 w-8 rounded-full object-cover">
            @else
                {{-- Mismo color de acento que la etiqueta del área, arriba del video --}}
                <div class="flex h-8 w-8 items-center justify-center rounded-full border border-[#5b5ef4]/25 bg-[#5b5ef4]/7 text-sm font-bold text-[#5b5ef4] dark:border-[#818cf8]/25 dark:bg-[#818cf8]/8 dark:text-[#818cf8]">
                    {{ str($comment->commenter?->getCommentDisplayName() ?? '?')->substr(0, 1)->upper() }}
                </div>
            @endif
        </div>

        <div class="min-w-0 flex-1">
            {{-- Deleted placeholder --}}
            @if ($comment->trashed())
                <p class="text-sm italic text-gray-400 dark:text-gray-500">{{ __('comments::comments.deleted_inline') }}</p>
            @else
                {{-- Header: name + timestamp --}}
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">
                        {{ $comment->commenter?->getCommentDisplayName() ?? __('comments::comments.unknown_user') }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400" title="{{ $comment->created_at->format('M j, Y g:i A') }}">
                        {{ $comment->created_at->diffForHumans() }}
                    </span>
                    @if ($comment->isEdited())
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ __('comments::comments.edited_marker') }}</span>
                    @endif
                </div>

                {{-- Body or edit form --}}
                @if ($isEditing)
                    <div class="mt-1">
                        {{ $this->editForm }}

                        @php $fileAttachments = $comment->attachments->filter(fn($a) => !$a->isImage()) @endphp
                        @if ($fileAttachments->isNotEmpty())
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($fileAttachments as $attachment)
                                    <a href="{{ $attachment->url() }}" target="_blank" rel="noopener noreferrer" download="{{ $attachment->original_name }}"
                                        class="flex items-center gap-2 rounded border border-gray-200 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        <span class="truncate">{{ $attachment->original_name }}</span>
                                        <span class="shrink-0 text-xs text-gray-400 dark:text-gray-500">({{ $attachment->formattedSize() }})</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-2 flex items-center justify-between">
                            <button type="button" wire:click="cancelEdit" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400">{{ __('comments::comments.actions.cancel') }}</button>
                            <button type="button" wire:click="saveEdit" class="text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">{{ __('comments::comments.actions.save') }}</button>
                        </div>
                    </div>
                @else
                    <div class="fi-prose prose prose-sm mt-1 max-w-none text-gray-700 dark:prose-invert dark:text-gray-300">
                        {!! $comment->renderBodyWithMentions() !!}
                    </div>

                    {{-- Attachments --}}
                    @if ($comment->attachments->isNotEmpty())
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($comment->attachments as $attachment)
                                @if ($attachment->isImage())
                                    <a href="{{ $attachment->url() }}" target="_blank" rel="noopener noreferrer" class="block">
                                        <img src="{{ $attachment->url() }}" alt="{{ $attachment->original_name }}"
                                            class="max-h-[200px] rounded border border-gray-200 object-cover dark:border-gray-600" />
                                    </a>
                                @else
                                    <a href="{{ $attachment->url() }}" target="_blank" rel="noopener noreferrer" download="{{ $attachment->original_name }}"
                                        class="flex items-center gap-2 rounded border border-gray-200 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        <span class="truncate">{{ $attachment->original_name }}</span>
                                        <span class="shrink-0 text-xs text-gray-400 dark:text-gray-500">({{ $attachment->formattedSize() }})</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    {{-- Reacciones + acciones (Responder, Editar, Eliminar, Fijar)
                         en una sola fila. "Responder" lleva un ícono de mensaje,
                         como en la referencia. --}}
                    <div class="mt-2 flex flex-wrap items-center gap-3">
                        <livewire:reactions :comment="$comment" :key="'reactions-'.$comment->id" />

                        @auth
                            @if ($comment->canReply())
                                @can('reply', $comment)
                                    <button wire:click="startReply" type="button"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                        <svg class="h-3.5 w-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 11.5a7.5 7.5 0 0 1-7.5 7.5 8 8 0 0 1-3-.6L4 20l1.6-4.5A7.5 7.5 0 1 1 20 11.5Z" />
                                        </svg>
                                        {{ __('comments::comments.actions.reply') }}
                                    </button>
                                @endcan
                            @endif

                            @can('update', $comment)
                                <button wire:click="startEdit" type="button"
                                    class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                    {{ __('comments::comments.actions.edit') }}
                                </button>
                            @endcan

                            @can('delete', $comment)
                                <button wire:click="deleteComment"
                                    wire:confirm="{{ __('comments::comments.actions.confirm_delete') }}"
                                    type="button"
                                    class="text-xs text-danger-600 hover:text-danger-500 dark:text-danger-400 dark:hover:text-danger-300">
                                    {{ __('comments::comments.actions.delete') }}
                                </button>
                            @endcan

                            @php
                                $authenticatedUser = \Relaticle\Comments\CommentsConfig::resolveAuthenticatedUser();
                            @endphp
                            @if ($authenticatedUser && \Relaticle\Comments\CommentsConfig::canPin($authenticatedUser, $comment))
                                @if ($comment->isPinned())
                                    <button wire:click="$parent.unpinComment({{ $comment->id }})" type="button"
                                        class="text-xs text-amber-600 hover:text-amber-500 dark:text-amber-400 dark:hover:text-amber-300">
                                        {{ __('comments::comments.actions.unpin') }}
                                    </button>
                                @else
                                    <button wire:click="$parent.pinComment({{ $comment->id }})" type="button"
                                        class="text-xs text-gray-400 hover:text-amber-500 dark:text-gray-500 dark:hover:text-amber-400">
                                        {{ __('comments::comments.actions.pin') }}
                                    </button>
                                @endif
                            @endif
                        @endauth
                    </div>
                @endif
            @endif

            {{-- Reply form --}}
            @if ($isReplying)
                <div class="mt-3"
                    x-data="{ uploadError: null }"
                    x-on:livewire-upload-error.window="uploadError = {{ Illuminate\Support\Js::from(__('comments::comments.attachments.upload_failed')) }}"
                    x-on:livewire-upload-start.window="uploadError = null"
                >
                    {{ $this->replyForm }}

                    @if (!empty($replyAttachments))
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($replyAttachments as $index => $file)
                                <div class="flex items-center gap-1 rounded bg-gray-100 px-2 py-1 text-xs text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    <span>{{ $file->getClientOriginalName() }}</span>
                                    <button type="button" wire:click="removeReplyAttachment({{ $index }})" class="text-gray-400 hover:text-danger-500 dark:text-gray-500 dark:hover:text-danger-400">&times;</button>
                                </div>
                            @endforeach
                        </div>

                        @error('replyAttachments.*')
                            <p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                        @enderror
                        <p x-show="uploadError" x-text="uploadError" class="mt-1 text-sm text-danger-600 dark:text-danger-400"></p>
                    @endif

                    <div class="mt-2 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if (\Relaticle\Comments\CommentsConfig::areAttachmentsEnabled())
                                <label class="flex cursor-pointer items-center gap-1.5 text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                                    </svg>
                                    {{ __('comments::comments.attachments.attach') }}
                                    <input type="file" wire:model="replyAttachments" multiple class="hidden" accept="{{ implode(',', \Relaticle\Comments\CommentsConfig::getAttachmentAllowedTypes()) }}" />
                                </label>
                            @endif
                            <button type="button" wire:click="cancelReply" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400">{{ __('comments::comments.actions.cancel') }}</button>
                        </div>
                        <button type="button" wire:click="addReply" class="text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">{{ __('comments::comments.actions.reply') }}</button>
                    </div>
                </div>
            @endif

            {{-- Un solo botón que cambia de texto/ícono (Ver ↔ Ocultar), arriba
                 de la lista — no dos botones separados, uno al inicio y otro
                 al final. --}}
            @if ($hasReplies)
                <button type="button" x-on:click="showReplies = !showReplies"
                    class="mt-2 inline-flex items-center gap-1.5 text-xs font-bold text-[#5b5ef4] hover:text-[#4643d4] dark:text-[#818cf8] dark:hover:text-[#a5b4fc]">
                    <svg class="h-3.5 w-3.5 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': showReplies }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                    <span x-text="(showReplies ? 'Ocultar ' : 'Ver ') + {{ $comment->replies->count() }} + ' {{ $comment->replies->count() === 1 ? 'respuesta' : 'respuestas' }}'"></span>
                </button>
            @endif
        </div> {{-- .min-w-0.flex-1 --}}
    </div> {{-- .thread-main --}}

    {{-- Las respuestas se renderizan FUERA del flex del comentario padre.
         Esto alinea cada codo con el centro del avatar padre, incluso con
         respuestas largas y respuestas a respuestas. --}}
    @if ($hasReplies)
        <div x-show="showReplies" x-cloak class="thread-children">
            @foreach ($comment->replies as $reply)
                <div class="thread-child{{ $loop->last ? ' thread-child-last' : '' }}">
                    <livewire:comment-item :comment="$reply" :key="'comment-'.$reply->id" />
                </div>
            @endforeach
        </div>
    @endif
</div>
