<x-filament-panels::page>
    {{-- <x-filament-panels::form wire:submit="guardar"> --}}
    <form wire:submit="guardar" id="form" class="grid gap-y-6">
        {{ $this->form }}

        <x-filament::actions :actions="$this->getFormActions()" alignment="center" />
    </form>
    {{-- </x-filament-panels::form> --}}
</x-filament-panels::page>
