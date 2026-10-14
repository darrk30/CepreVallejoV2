<x-filament-panels::page>
    @php $stats = $this->getStats(); @endphp

    <div class="font-handlee pb-[60px] text-[#0c0b1a] dark:text-[#edecf8]">

        {{-- MASTHEAD ── --}}
        <div class="mb-[5px] flex flex-wrap items-center justify-between gap-5 border-b border-[#6366f1]/14 pb-2.5 dark:border-[#818cf8]/12">
            <div class="min-w-0 flex-1">
                <p class="m-0 mb-1.5 text-[0.62rem] font-bold uppercase tracking-[0.15em] text-[#5b5ef4] dark:text-[#818cf8]">Cepre Vallejo · Panel Docente</p>
                <h1 class="m-0 text-[clamp(1.8rem,4vw,2.8rem)] font-extrabold leading-[1.1] tracking-[-0.02em] text-[#0c0b1a] dark:text-[#edecf8]">Mis <em class="italic text-[#5b5ef4] dark:text-[#818cf8]">Pagos</em></h1>
                <p class="mt-2 text-[0.72rem] font-semibold text-[#8b88b0] dark:text-[#636086]">
                    {{ $stats['count'] }} comprobante{{ $stats['count'] !== 1 ? 's' : '' }} registrado{{ $stats['count'] !== 1 ? 's' : '' }}
                </p>
            </div>
            <div class="flex h-[60px] w-[60px] shrink-0 items-center justify-center rounded-2xl border border-[#5b5ef4]/22 bg-[#5b5ef4]/7 dark:border-[#818cf8]/22 dark:bg-[#818cf8]/9">
                <svg class="h-7 w-7 text-[#5b5ef4] dark:text-[#818cf8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                        d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-4.5-9.75h16.5a1.5 1.5 0 0 1 1.5 1.5v9a1.5 1.5 0 0 1-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5v-9a1.5 1.5 0 0 1 1.5-1.5Z" />
                </svg>
            </div>
        </div>

        {{-- ESTADÍSTICAS ── --}}
        <div class="mt-7 grid grid-cols-1 gap-3.5 sm:grid-cols-3">
            <div class="rounded-[16px] border border-[#6366f1]/14 bg-[#f9f8ff] p-[18px] dark:border-[#818cf8]/12 dark:bg-[#1b1a2e]">
                <span class="text-[0.63rem] font-bold uppercase tracking-[0.12em] text-[#8b88b0] dark:text-[#636086]">Total Percibido</span>
                <h2 class="m-0 mt-1 text-[1.5rem] font-extrabold text-[#5b5ef4] dark:text-[#818cf8]">S/ {{ number_format($stats['total'], 2) }}</h2>
            </div>
            <div class="rounded-[16px] border border-[#6366f1]/14 bg-[#f9f8ff] p-[18px] dark:border-[#818cf8]/12 dark:bg-[#1b1a2e]">
                <span class="text-[0.63rem] font-bold uppercase tracking-[0.12em] text-[#8b88b0] dark:text-[#636086]">Último Pago</span>
                <h2 class="m-0 mt-1 text-[1.5rem] font-extrabold text-[#0c0b1a] dark:text-[#edecf8]">S/ {{ number_format($stats['last_amount'], 2) }}</h2>
                <p class="m-0 mt-1 text-[0.7rem] text-[#8b88b0] dark:text-[#636086]">Recibido el {{ $stats['last_date'] }}</p>
            </div>
            <div class="rounded-[16px] border border-[#6366f1]/14 bg-[#f9f8ff] p-[18px] dark:border-[#818cf8]/12 dark:bg-[#1b1a2e]">
                <span class="text-[0.63rem] font-bold uppercase tracking-[0.12em] text-[#8b88b0] dark:text-[#636086]">Pagos Registrados</span>
                <h2 class="m-0 mt-1 text-[1.5rem] font-extrabold text-[#0c0b1a] dark:text-[#edecf8]">{{ $stats['count'] }}</h2>
            </div>
        </div>

        {{-- TÍTULO DE SECCIÓN ── --}}
        <div class="mt-11 mb-[18px] flex items-center gap-3.5">
            <h2 class="m-0 whitespace-nowrap text-[1.2rem] font-bold text-[#0c0b1a] dark:text-[#edecf8]">🧾 Mis Comprobantes de Pago</h2>
            <span class="h-px flex-1 bg-[#6366f1]/14 dark:bg-[#818cf8]/12"></span>
        </div>

        {{-- LISTADO ── --}}
        <div class="flex flex-col gap-3.5">
            @forelse($this->getPayments() as $payment)
                <div class="overflow-hidden rounded-[16px] border border-[#6366f1]/14 bg-white transition-colors duration-200 hover:border-[#5b5ef4]/30 dark:border-[#818cf8]/12 dark:bg-[#151424] dark:hover:border-[#818cf8]/30">
                    <div class="flex flex-wrap items-center justify-between gap-4 p-[18px] max-[600px]:flex-col max-[600px]:items-start">
                        {{-- Izquierda: fecha e info --}}
                        <div class="flex items-center gap-4">
                            <div class="flex h-[52px] w-[52px] shrink-0 flex-col items-center justify-center rounded-[12px] bg-[#5b5ef4]/9 text-[#5b5ef4] dark:bg-[#818cf8]/13 dark:text-[#818cf8]">
                                <span class="text-[0.6rem] font-extrabold uppercase">{{ $payment->fecha_pago->format('M') }}</span>
                                <span class="text-[1.2rem] leading-none font-extrabold">{{ $payment->fecha_pago->format('d') }}</span>
                            </div>
                            <div class="min-w-0">
                                <h4 class="m-0 text-[0.95rem] font-extrabold text-[#0c0b1a] dark:text-[#edecf8]">Pago por Honorarios</h4>
                                <p class="m-0 mt-0.5 text-[0.8rem] text-[#8b88b0] dark:text-[#636086]">
                                    {{ $payment->metodo_pago }} · <span class="font-semibold">N° {{ $payment->numero_operacion ?? 'S/N' }}</span>
                                </p>
                                @if ($payment->academicCycle)
                                    <span class="mt-1.5 inline-block rounded-[5px] bg-[#f3f4f6] px-2 py-0.5 text-[0.57rem] font-extrabold text-[#4b5563] dark:bg-[#221f3a] dark:text-[#a9a6cc]">{{ $payment->academicCycle->nombre }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Derecha: monto y acciones --}}
                        <div class="flex items-center gap-7 max-[600px]:w-full max-[600px]:justify-between max-[600px]:border-t max-[600px]:border-dashed max-[600px]:border-[#6366f1]/20 max-[600px]:pt-3.5 dark:max-[600px]:border-[#818cf8]/18">
                            <div class="text-right max-[600px]:text-left">
                                <span class="block text-[1.3rem] font-extrabold text-emerald-600 dark:text-emerald-400">S/ {{ number_format($payment->monto, 2) }}</span>
                                <span class="rounded-[4px] bg-emerald-50 px-1.5 py-[1px] text-[0.57rem] font-extrabold text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300">COMPLETADO</span>
                            </div>
                            @if ($payment->comprobante_path)
                                <a href="{{ Storage::url($payment->comprobante_path) }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 rounded-[10px] bg-[#5b5ef4] px-[18px] py-2.5 text-[0.81rem] font-bold text-white no-underline transition-colors duration-150 hover:bg-[#4643d4] hover:text-white hover:no-underline dark:bg-[#818cf8] dark:hover:bg-[#a5b4fc]">
                                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                    Voucher
                                </a>
                            @else
                                <span class="text-[0.8rem] text-[#8b88b0] italic dark:text-[#636086]">Sin archivo</span>
                            @endif
                        </div>
                    </div>

                    @if ($payment->observaciones)
                        <div class="border-t border-[#6366f1]/14 bg-[#f9f8ff] px-[18px] py-3 text-[0.8rem] text-[#4b5563] dark:border-[#818cf8]/12 dark:bg-[#1b1a2e] dark:text-[#a9a6cc]">
                            <strong class="text-[#0c0b1a] dark:text-[#edecf8]">Nota:</strong> {{ $payment->observaciones }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full rounded-[14px] border-[1.5px] border-dashed border-[#6366f1]/28 bg-[#f9f8ff] px-8 py-[72px] text-center dark:border-[#818cf8]/28 dark:bg-[#1b1a2e]">
                    <svg class="mx-auto mb-3.5 h-11 w-11 text-[#5b5ef4] opacity-[0.22] dark:text-[#818cf8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3"
                            d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-4.5-9.75h16.5a1.5 1.5 0 0 1 1.5 1.5v9a1.5 1.5 0 0 1-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5v-9a1.5 1.5 0 0 1 1.5-1.5Z" />
                    </svg>
                    <h3 class="m-0 mb-1 text-[0.9rem] font-bold text-[#3b3866] dark:text-[#a9a6cc]">Sin registros</h3>
                    <p class="m-0 text-[0.75rem] text-[#8b88b0] dark:text-[#636086]">No se encontraron pagos registrados todavía.</p>
                </div>
            @endforelse
        </div>

    </div>
</x-filament-panels::page>
