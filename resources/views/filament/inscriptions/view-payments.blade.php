@push('styles')
    <link rel="stylesheet" href="{{ asset('css/view-payments.css') }}">
@endpush
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