<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Reçu {{ $payment->receipt_number }} - {{ $payment->business->name_fr }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #0f172a; margin: 0; padding: 25px; font-size: 13px; }
        .receipt-card { max-width: 720px; margin: 0 auto; border: 2px solid #0f172a; padding: 30px; border-radius: 8px; background: #fff; }
        .rep-header { text-align: center; font-size: 11px; text-transform: uppercase; color: #475569; font-weight: bold; margin-bottom: 12px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px; }
        .biz-title { font-size: 18px; font-weight: 800; margin: 0 0 4px 0; color: #0f172a; }
        .biz-meta { font-size: 11px; color: #64748b; line-height: 1.4; }
        .receipt-badge { text-align: right; }
        .receipt-title { font-size: 20px; font-weight: 900; color: #047857; margin: 0; }
        .receipt-title-ar { font-size: 14px; font-weight: bold; color: #0f172a; direction: rtl; }
        .receipt-num { font-family: monospace; font-size: 14px; font-weight: bold; color: #334155; margin-top: 2px; }
        .data-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #cbd5e1; }
        .data-label { color: #64748b; font-size: 12px; }
        .data-value { font-weight: bold; color: #0f172a; font-size: 13px; text-align: right; }
        .amount-banner { background: #f0fdf4; border: 2px solid #86efac; border-radius: 8px; padding: 15px; margin: 20px 0; text-align: center; }
        .amount-num { font-size: 26px; font-weight: 900; color: #047857; font-family: monospace; }
        .amount-words { font-size: 12px; color: #166534; font-style: italic; margin-top: 4px; }
        .financial-recap { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 16px; margin: 20px 0; }
        .recap-table { width: 100%; border-collapse: collapse; }
        .recap-table td { padding: 4px 6px; font-size: 12px; }
        .signatures { display: flex; justify-content: space-between; margin-top: 30px; }
        .sig-box { width: 45%; border-top: 1px dashed #94a3b8; padding-top: 8px; text-align: center; font-size: 11px; color: #475569; height: 70px; }
        .no-print-bar { background: #0f172a; color: white; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-radius: 6px; }
        .btn { background: #059669; color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: bold; cursor: pointer; text-decoration: none; font-size: 12px; }
        @media print {
            body { padding: 0; }
            .receipt-card { border: 1px solid #000; padding: 20px; }
            .no-print-bar { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <span>Aperçu du Reçu de Versement Officiel</span>
        <div>
            <button onclick="window.print()" class="btn">🖨️ Imprimer le Reçu</button>
            <a href="{{ route('reservations.show', $payment->reservation) }}" style="color: #cbd5e1; margin-left: 15px; text-decoration: none; font-size: 12px;">← Dossier 360°</a>
        </div>
    </div>

    <div class="receipt-card">
        <div class="rep-header">
            République Algérienne Démocratique et Populaire • الجمهورية الجزائرية الديمقراطية الشعبية
        </div>

        <div class="header">
            <div>
                <h1 class="biz-title">{{ $payment->business->name_fr }}</h1>
                <div class="biz-meta">
                    {{ $payment->business->address_line }}, {{ $payment->business->wilaya }}<br>
                    Tél : {{ $payment->business->phone }}<br>
                    @if($payment->business->rc)RC : {{ $payment->business->rc }} | @endif
                    @if($payment->business->nif)NIF : {{ $payment->business->nif }}@endif
                </div>
            </div>

            <div class="receipt-badge">
                <div class="receipt-title">REÇU DE VERSEMENT</div>
                <div class="receipt-title-ar">وصل استلام مبلغ مالي</div>
                <div class="receipt-num">N° {{ $payment->receipt_number }}</div>
                <div style="font-size: 11px; color: #64748b; margin-top: 3px;">
                    Date : {{ $payment->payment_date->format('d/m/Y') }}
                </div>
            </div>
        </div>

        <!-- Details -->
        <div>
            <div class="data-row">
                <span class="data-label">Reçu de M./Mme :</span>
                <span class="data-value">{{ $payment->reservation->customer_name }}</span>
            </div>

            <div class="data-row">
                <span class="data-label">Pour la réservation N° :</span>
                <span class="data-value font-mono">{{ $payment->reservation->reservation_number }} (Salle: {{ $payment->reservation->space->name }})</span>
            </div>

            <div class="data-row">
                <span class="data-label">Date de l'événement :</span>
                <span class="data-value">{{ \Carbon\Carbon::parse($payment->reservation->event_date)->translatedFormat('l d F Y') }}</span>
            </div>

            <div class="data-row">
                <span class="data-label">Nature du versement :</span>
                <span class="data-value">{{ $payment->typeLabel() }}</span>
            </div>

            <div class="data-row">
                <span class="data-label">Mode de règlement :</span>
                <span class="data-value">
                    {{ $payment->methodLabel() }}
                    @if($payment->cheque_number) (N° {{ $payment->cheque_number }} - Banque : {{ $payment->bank_name }})@endif
                </span>
            </div>
        </div>

        <!-- Amount Box -->
        <div class="amount-banner">
            <span style="font-size: 11px; text-transform: uppercase; color: #166534; font-weight: bold; letter-spacing: 0.5px;">Montant Reçu</span>
            <div class="amount-num">{{ number_format($payment->amount, 2, ',', ' ') }} DA</div>
            @if($payment->notes)
                <div class="amount-words">Note : {{ $payment->notes }}</div>
            @endif
        </div>

        <!-- Financial Position Recap -->
        <div class="financial-recap">
            <div style="font-size: 11px; font-weight: bold; text-transform: uppercase; color: #475569; margin-bottom: 6px;">
                Situation Financière de la Réservation
            </div>
            <table class="recap-table">
                <tr>
                    <td style="color: #64748b;">Montant total convenu :</td>
                    <td style="text-align: right; font-weight: bold; font-family: monospace;">
                        {{ number_format($payment->reservation->total_price, 2, ',', ' ') }} DA
                    </td>
                </tr>
                <tr>
                    <td style="color: #64748b;">Total versé à ce jour (cumulé) :</td>
                    <td style="text-align: right; font-weight: bold; color: #047857; font-family: monospace;">
                        {{ number_format($payment->reservation->total_paid, 2, ',', ' ') }} DA
                    </td>
                </tr>
                <tr style="border-top: 1px solid #cbd5e1;">
                    <td><strong>Solde restant dû :</strong></td>
                    <td style="text-align: right; font-weight: bold; font-family: monospace; font-size: 13px; color: {{ $payment->reservation->remaining_balance > 0 ? '#b45309' : '#047857' }};">
                        {{ number_format($payment->reservation->remaining_balance, 2, ',', ' ') }} DA
                    </td>
                </tr>
            </table>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="sig-box">
                Signature du Client
            </div>
            <div class="sig-box">
                Le Caissier / Direction (Cachet & Signature)<br>
                <span style="font-size: 10px; color: #94a3b8;">Encaissé par : {{ $payment->receiver->name }}</span>
            </div>
        </div>
    </div>

</body>
</html>
