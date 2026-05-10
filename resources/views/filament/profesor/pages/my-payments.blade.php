<x-filament-panels::page>
    <div class="payments-container">
        
        {{-- Estadísticas --}}
        @php $stats = $this->getStats(); @endphp
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-label">Total Percibido</span>
                <h2 class="stat-value primary">S/ {{ number_format($stats['total'], 2) }}</h2>
            </div>
            <div class="stat-card">
                <span class="stat-label">Último Pago</span>
                <h2 class="stat-value">S/ {{ number_format($stats['last_amount'], 2) }}</h2>
                <p class="stat-footer">Recibido el {{ $stats['last_date'] }}</p>
            </div>
            <div class="stat-card">
                <span class="stat-label">Pagos Registrados</span>
                <h2 class="stat-value">{{ $stats['count'] }}</h2>
            </div>
        </div>

        {{-- Título de Sección --}}
        <div class="section-header">
            <svg class="header-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-1.234" />
            </svg>
            <h3>Mis Comprobantes de Pago</h3>
        </div>

        {{-- Listado de Tarjetas --}}
        <div class="payments-list">
            @forelse($this->getPayments() as $payment)
                <div class="payment-card">
                    <div class="card-main">
                        {{-- Lado Izquierdo: Fecha e Info --}}
                        <div class="card-left">
                            <div class="date-badge">
                                <span class="month">{{ $payment->fecha_pago->format('M') }}</span>
                                <span class="day">{{ $payment->fecha_pago->format('d') }}</span>
                            </div>
                            <div class="info-group">
                                <h4>Pago por Honorarios</h4>
                                <p class="method">{{ $payment->metodo_pago }} • <span class="op-number">N° {{ $payment->numero_operacion ?? 'S/N' }}</span></p>
                                @if ($payment->academicCycle)
                                    <span class="cycle-tag">{{ $payment->academicCycle->nombre }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Lado Derecho: Monto y Acciones --}}
                        <div class="card-right">
                            <div class="amount-group">
                                <span class="amount">S/ {{ number_format($payment->monto, 2) }}</span>
                                <span class="status">COMPLETADO</span>
                            </div>
                            <div class="action-group">
                                @if ($payment->comprobante_path)
                                    <a href="{{ Storage::url($payment->comprobante_path) }}" target="_blank" class="btn-download">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="btn-icon">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                        </svg>
                                        Voucher
                                    </a>
                                @else
                                    <span class="no-file">Sin archivo</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Descripción / Observaciones (Opcional) --}}
                    @if($payment->observaciones)
                        <div class="card-description">
                            <strong>Nota:</strong> {{ $payment->observaciones }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="empty-container">
                    <p>No se encontraron registros de pagos.</p>
                </div>
            @endforelse
        </div>
    </div>

    <style>
/* --- CONFIGURACIÓN GENERAL --- */
.payments-container { display: flex; flex-direction: column; gap: 1.5rem; color: #1f2937; }

/* --- STATS (IGUAL) --- */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
.stat-card { background: white; padding: 1.2rem; border-radius: 1rem; border: 1px solid #e5e7eb; }
.stat-label { font-size: 0.7rem; font-weight: 700; color: #6b7280; text-transform: uppercase; }
.stat-value { font-size: 1.5rem; font-weight: 900; margin-top: 0.2rem; }
.stat-value.primary { color: #2563eb; }

/* --- HEADER --- */
.section-header { display: flex; align-items: center; gap: 0.5rem; margin-top: 1rem; }
.header-icon { width: 22px; height: 22px; color: #2563eb; }
.section-header h3 { font-size: 1.1rem; font-weight: 800; margin: 0; }

/* --- LAS TARJETAS (CARDS) --- */
.payments-list { display: flex; flex-direction: column; gap: 1rem; }

.payment-card {
    background: white;
    border-radius: 1.2rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    transition: all 0.3s ease;
    overflow: hidden;
}

.payment-card:hover {
    border-color: #3b82f6;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
}

.card-main {
    padding: 1.2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

/* Lado Izquierdo */
.card-left { display: flex; align-items: center; gap: 1rem; }
.date-badge {
    width: 52px; height: 52px; background: #f0f7ff; color: #2563eb;
    border-radius: 0.8rem; display: flex; flex-direction: column;
    align-items: center; justify-content: center; flex-shrink: 0;
}
.date-badge .month { font-size: 0.6rem; font-weight: 800; text-transform: uppercase; }
.date-badge .day { font-size: 1.2rem; font-weight: 900; line-height: 1; }

.info-group h4 { font-size: 1rem; font-weight: 800; margin: 0; }
.method { font-size: 0.85rem; color: #6b7280; margin: 2px 0; }
.cycle-tag {
    display: inline-block; padding: 2px 8px; background: #f3f4f6;
    color: #4b5563; font-size: 9px; font-weight: 800; border-radius: 5px;
}

/* Lado Derecho */
.card-right { display: flex; align-items: center; gap: 2rem; }
.amount-group { text-align: right; }
.amount { display: block; font-size: 1.4rem; font-weight: 900; color: #10b981; }
.status { font-size: 9px; font-weight: 800; color: #059669; background: #ecfdf5; padding: 1px 6px; border-radius: 4px; }

/* Botón */
.btn-download {
    display: flex; align-items: center; gap: 0.4rem;
    padding: 0.6rem 1.2rem; background: #1e293b; color: white;
    font-size: 0.85rem; font-weight: 700; border-radius: 0.7rem;
    text-decoration: none; transition: 0.2s;
}
.btn-download:hover { background: #000; }
.btn-icon { width: 16px; height: 16px; }
.no-file { font-size: 0.8rem; color: #9ca3af; font-style: italic; }

/* Descripción */
.card-description {
    background: #f9fafb; padding: 0.8rem 1.2rem;
    font-size: 0.85rem; color: #4b5563; border-top: 1px solid #f3f4f6;
    line-height: 1.5;
}

/* --- RESPONSIVE (MÓVIL) --- */
@media (max-width: 768px) {
    .card-main { flex-direction: column; align-items: flex-start; gap: 1.2rem; }
    .card-right { width: 100%; justify-content: space-between; border-top: 1px dashed #e5e7eb; padding-top: 1rem; }
    .amount-group { text-align: left; }
    .amount { font-size: 1.2rem; }
    .btn-download { padding: 0.5rem 1rem; }
}

@media (min-width: 769px) {
    .payment-card { margin: 0 0.5rem; }
}
    </style>
</x-filament-panels::page>