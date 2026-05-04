<style>
    .tukipu-modal-container {
        padding: 8px;
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .payments-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        background-color: #ffffff;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .payments-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 0.875rem;
    }

    .payments-table thead tr {
        background-color: #f9fafb;
        color: #6b7280;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

    .payments-table th {
        padding: 12px 16px;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
    }

    .payments-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
    }

    .amount-cell {
        font-weight: 700;
        color: #111827;
    }

    .date-cell {
        color: #4b5563;
        white-space: nowrap;
    }

    .badge-method {
        display: inline-block;
        background-color: #f0f9ff;
        color: #0369a1;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        border: 1px solid #e0f2fe;
    }

    .empty-state {
        padding: 32px 16px;
        text-align: center;
        color: #9ca3af;
        font-style: italic;
    }

    /* --- SOPORTE MODO OSCURO FILAMENT --- */
    /* Targeteamos la clase .dark que Filament aplica al tag html */
    .dark .payments-card {
        background-color: #18181b; /* Zinc 900 */
        border-color: #3f3f46;     /* Zinc 700 */
    }

    .dark .payments-table thead tr {
        background-color: #27272a; /* Zinc 800 */
        color: #a1a1aa;           /* Zinc 400 */
    }

    .dark .payments-table th {
        border-bottom-color: #3f3f46;
    }

    .dark .payments-table td {
        border-bottom-color: #27272a;
        color: #d4d4d8;           /* Zinc 300 */
    }

    .dark .amount-cell {
        color: #ffffff;
    }

    .dark .date-cell {
        color: #a1a1aa;
    }

    .dark .badge-method {
        background-color: rgba(3, 105, 161, 0.2);
        color: #7dd3fc;
        border-color: rgba(3, 105, 161, 0.3);
    }

    .dark .empty-state {
        color: #71717a;
    }
</style>

<div class="tukipu-modal-container">
    <div class="payments-card">
        <table class="payments-table">
            <thead>
                <tr>
                    <th>Monto</th>
                    <th>Fecha</th>
                    <th>Método</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td class="amount-cell">
                            S/ {{ number_format($payment->monto, 2) }}
                        </td>
                        <td class="date-cell">
                            {{ $payment->fecha_pago->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <span class="badge-method">
                                {{ $payment->metodo_pago }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty-state">
                            No hay registros de pago disponibles.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>