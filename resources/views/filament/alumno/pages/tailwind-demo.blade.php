<x-filament-panels::page>
    <div class="space-y-8">
        <div class="rounded-3xl border border-dashed border-amber-400 bg-amber-50 p-6 text-amber-900 dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-200">
            <p class="text-sm">
                Esta página es solo una prueba temporal para confirmar que Tailwind funciona dentro
                del panel de Filament (gracias al theme registrado con <code>-&gt;viteTheme()</code>
                en <code>AlumnoPanelProvider</code>). Si ves las tarjetas de abajo con degradados,
                bordes redondeados, rotación y sombras de color, Tailwind está compilando tus clases
                correctamente. Puedes borrar esta página y su ruta del menú cuando termines de revisar.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-2xl bg-gradient-to-br from-fuchsia-500 via-purple-600 to-indigo-600 p-6 text-white shadow-xl shadow-purple-500/30 transition hover:-translate-y-1 hover:shadow-2xl">
                <h3 class="text-lg font-bold">Gradiente + sombra de color</h3>
                <p class="mt-2 text-sm text-white/80">bg-gradient-to-br, shadow-purple-500/30, hover:-translate-y-1</p>
            </div>

            <div class="-rotate-2 rounded-2xl border-4 border-dashed border-emerald-400 bg-white p-6 shadow-lg dark:bg-slate-900">
                <h3 class="text-lg font-bold text-emerald-600 dark:text-emerald-400">Rotación + borde punteado</h3>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">-rotate-2, border-dashed, border-emerald-400</p>
            </div>

            <div class="rounded-2xl bg-[#0f172a] p-6 text-white ring-4 ring-cyan-400/50">
                <h3 class="text-lg font-bold text-cyan-300">Color arbitrario + ring</h3>
                <p class="mt-2 text-sm text-slate-300">bg-[#0f172a], ring-4, ring-cyan-400/50</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button class="rounded-full bg-rose-600 px-5 py-2 font-semibold text-white shadow-md transition hover:bg-rose-700 active:scale-95">
                Botón rosa (hover + active:scale-95)
            </button>
            <button class="rounded-full border-2 border-slate-900 px-5 py-2 font-semibold text-slate-900 transition hover:bg-slate-900 hover:text-white dark:border-white dark:text-white dark:hover:bg-white dark:hover:text-slate-900">
                Botón outline (dark mode)
            </button>
        </div>
    </div>
</x-filament-panels::page>
