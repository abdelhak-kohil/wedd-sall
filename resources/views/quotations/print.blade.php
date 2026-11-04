<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Devis {{ $quotation->quote_number }} - {{ $quotation->business->name_fr }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 20px;
            font-size: 13px;
            background-color: #f8fafc;
        }
        .container {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 35px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #059669;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .biz-title {
            font-size: 22px;
            font-weight: 800;
            color: #064e3b;
            margin: 0 0 6px 0;
            letter-spacing: -0.5px;
        }
        .biz-meta {
            font-size: 11px;
            color: #475569;
            line-height: 1.6;
        }
        .doc-badge {
            text-align: right;
        }
        .doc-title {
            font-size: 26px;
            font-weight: 900;
            color: #059669;
            margin: 0;
            letter-spacing: 1px;
        }
        .doc-number {
            font-size: 15px;
            font-family: 'Consolas', 'Courier New', monospace;
            font-weight: bold;
            color: #1e293b;
            margin-top: 3px;
        }
        .grid-2 {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            gap: 20px;
        }
        .box {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 14px 18px;
            border-radius: 8px;
        }
        .box-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #059669;
            margin-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #f1f5f9;
            padding: 10px 12px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #334155;
            border-bottom: 2px solid #cbd5e1;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .totals {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 25px;
        }
        .totals-table {
            width: 360px;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 5px 10px;
            border-bottom: 1px solid #f1f5f9;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .sig-box {
            width: 44%;
            border: 1px dashed #94a3b8;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
            font-size: 11px;
            color: #475569;
            height: 95px;
            background: #fafafa;
        }
        .no-print-bar {
            background: #0f172a;
            color: white;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-radius: 8px;
            max-width: 820px;
            margin-left: auto;
            margin-right: auto;
        }
        .btn {
            background: #059669;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn:hover {
            background: #047857;
        }
        @media print {
            body {
                padding: 0;
                background-color: #ffffff;
            }
            .container {
                border: none;
                padding: 0;
                max-width: 100%;
                box-shadow: none;
            }
            .no-print-bar {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <span>📄 <strong>Devis Commercial Officiel</strong> — {{ $quotation->quote_number }}</span>
        <div style="display: flex; align-items: center; gap: 15px;">
            <button onclick="window.print()" class="btn">🖨️ Imprimer / Enregistrer en PDF</button>
            <a href="{{ route('quotations.show', $quotation) }}" style="color: #cbd5e1; text-decoration: none; font-size: 12px;">← Retour au devis</a>
        </div>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div>
                <h1 class="biz-title">{{ $quotation->business->name_fr }}</h1>
                <div class="biz-meta">
                    @if($quotation->business->address_line){{ $quotation->business->address_line }}, @endif
                    {{ $quotation->business->wilaya }}<br>
                    Tél : {{ $quotation->business->phone }} | Email : {{ $quotation->business->email }}<br>
                    @if($quotation->business->rc)RC : {{ $quotation->business->rc }} | @endif
                    @if($quotation->business->nif)NIF : {{ $quotation->business->nif }} | @endif
                    @if($quotation->business->nis)NIS : {{ $quotation->business->nis }}@endif
                </div>
            </div>
            <div class="doc-badge">
                <h2 class="doc-title">DEVIS</h2>
                <div class="doc-number">{{ $quotation->quote_number }}</div>
                <div style="font-size: 11px; color: #475569; margin-top: 6px; line-height: 1.5;">
                    Date d'émission : <strong>{{ $quotation->created_at->format('d/m/Y') }}</strong><br>
                    Validité de l'offre : <strong>{{ $quotation->valid_until->format('d/m/Y') }}</strong>
                </div>
            </div>
        </div>

        <!-- Two Boxes -->
        <div class="grid-2">
            <div class="box">
                <div class="box-title">Client & Mariés</div>
                <div style="font-size: 14px; font-weight: bold; color: #0f172a;">{{ $quotation->customer_name }}</div>
                <div style="font-size: 12px; color: #475569; margin-top: 4px; line-height: 1.5;">
                    Téléphone : <strong>{{ $quotation->customer_phone }}</strong><br>
                    @if($quotation->groom_name || $quotation->bride_name)
                        Mariés : <strong>{{ $quotation->groom_name ?? '—' }} & {{ $quotation->bride_name ?? '—' }}</strong>
                    @endif
                </div>
            </div>

            <div class="box">
                <div class="box-title">Détails de l'événement</div>
                <div style="font-size: 13px; font-weight: bold; color: #0f172a;">
                    {{ $quotation->event_date->translatedFormat('l d F Y') }}
                </div>
                <div style="font-size: 12px; color: #475569; margin-top: 4px; line-height: 1.5;">
                    Espace : <strong>{{ $quotation->space->name }}</strong><br>
                    Horaires : <strong>{{ substr($quotation->start_time, 0, 5) }} - {{ substr($quotation->end_time, 0, 5) }}</strong><br>
                    Nombre d'invités estimé : <strong>{{ $quotation->expected_guests }} personnes</strong>
                </div>
            </div>
        </div>

        <!-- Table of Services -->
        <table>
            <thead>
                <tr>
                    <th style="width: 52%;">Désignation des Prestations</th>
                    <th style="text-align: center; width: 14%;">Quantité</th>
                    <th style="text-align: right; width: 17%;">Prix Unit. (DA)</th>
                    <th style="text-align: right; width: 17%;">Montant (DA)</th>
                </tr>
            </thead>
            <tbody>
                @php $items = $quotation->items_list; @endphp
                @if(count($items) > 0)
                    @foreach($items as $it)
                        <tr>
                            <td>
                                <strong style="color: #0f172a;">{{ $it['description'] ?? 'Prestation' }}</strong>
                            </td>
                            <td style="text-align: center; font-weight: bold;">{{ $it['quantity'] ?? 1 }}</td>
                            <td style="text-align: right; font-family: monospace;">
                                {{ number_format($it['unit_price'] ?? 0, 2, ',', ' ') }}
                            </td>
                            <td style="text-align: right; font-weight: bold; font-family: monospace;">
                                {{ number_format(($it['quantity'] ?? 1) * ($it['unit_price'] ?? 0), 2, ',', ' ') }} DA
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>
                            <strong style="color: #0f172a;">
                                {{ $quotation->package ? $quotation->package->name_fr : ('Mise à disposition de salle : ' . $quotation->space->name) }}
                            </strong>
                            <div style="font-size: 11px; color: #64748b; margin-top: 3px;">
                                Salle climatisée, tables, chaises avec housses, sonorisation.
                                @if($quotation->package && !empty($quotation->package->included_items))
                                    <br>Inclus : {{ implode(', ', $quotation->package->included_items) }}
                                @endif
                            </div>
                        </td>
                        <td style="text-align: center; font-weight: bold;">1</td>
                        <td style="text-align: right; font-family: monospace;">
                            {{ number_format($quotation->total_price, 2, ',', ' ') }}
                        </td>
                        <td style="text-align: right; font-weight: bold; font-family: monospace;">
                            {{ number_format($quotation->total_price, 2, ',', ' ') }} DA
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Totals Breakdown -->
        <div class="totals">
            <table class="totals-table">
                <tr>
                    <td>Sous-total HT :</td>
                    <td style="text-align: right; font-family: monospace; font-weight: bold;">
                        {{ number_format($quotation->subtotal > 0 ? $quotation->subtotal : $quotation->total_price, 2, ',', ' ') }} DA
                    </td>
                </tr>

                @if($quotation->discount_amount > 0)
                    <tr>
                        <td style="color: #b45309;">
                            Remise commerciale
                            @if($quotation->discount_type === 'percent') ({{ $quotation->discount_value }}%) @endif :
                        </td>
                        <td style="text-align: right; font-family: monospace; font-weight: bold; color: #b45309;">
                            - {{ number_format($quotation->discount_amount, 2, ',', ' ') }} DA
                        </td>
                    </tr>
                    <tr>
                        <td>Net Commercial HT :</td>
                        <td style="text-align: right; font-family: monospace; font-weight: bold;">
                            {{ number_format($quotation->net_before_tax, 2, ',', ' ') }} DA
                        </td>
                    </tr>
                @endif

                @if($quotation->tax_amount > 0 || $quotation->tax_rate > 0)
                    <tr>
                        <td style="color: #1d4ed8;">TVA ({{ $quotation->tax_rate }}%) :</td>
                        <td style="text-align: right; font-family: monospace; font-weight: bold; color: #1d4ed8;">
                            + {{ number_format($quotation->tax_amount, 2, ',', ' ') }} DA
                        </td>
                    </tr>
                @endif

                <tr style="border-top: 2px solid #059669; border-bottom: 2px solid #059669;">
                    <td style="padding: 8px 10px;"><strong>TOTAL GÉNÉRAL NET :</strong></td>
                    <td style="text-align: right; font-weight: 900; font-size: 16px; color: #047857; font-family: monospace; padding: 8px 10px;">
                        {{ number_format($quotation->total_price, 2, ',', ' ') }} DA
                    </td>
                </tr>
                <tr>
                    <td style="color: #475569; padding-top: 8px;">Acompte convenu ('Erboun') :</td>
                    <td style="text-align: right; font-weight: bold; font-family: monospace; padding-top: 8px;">
                        {{ number_format($quotation->deposit_required, 2, ',', ' ') }} DA
                    </td>
                </tr>
                <tr>
                    <td style="color: #475569;">Solde restant exigible au Jour J :</td>
                    <td style="text-align: right; font-family: monospace; color: #334155;">
                        {{ number_format($quotation->balance_due, 2, ',', ' ') }} DA
                    </td>
                </tr>
            </table>
        </div>

        <!-- Notes and terms -->
        @if($quotation->notes || $quotation->payment_terms)
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; margin-bottom: 20px; font-size: 11px; color: #475569; line-height: 1.5;">
                @if($quotation->notes)
                    <div><strong>Notes particulières :</strong> {{ $quotation->notes }}</div>
                @endif
                @if($quotation->payment_terms)
                    <div style="margin-top: 4px;"><strong>Conditions & Règlement :</strong> {{ $quotation->payment_terms }}</div>
                @endif
            </div>
        @endif

        <!-- Mentions légales -->
        <div style="font-size: 11px; color: #64748b; line-height: 1.5; border-left: 3px solid #059669; padding-left: 10px;">
            * Ce devis constitue une proposition tarifaire valable jusqu'au {{ $quotation->valid_until->format('d/m/Y') }}. La date ne sera définitivement réservée qu'après signature du contrat et encaissement de l'acompte convenu.<br>
            * Modalités de règlement acceptées : Espèces, Chèque certifié de banque ou Virement bancaire.
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="sig-box">
                <strong>Bon pour accord</strong><br>
                <span style="font-size: 10px; color: #64748b;">(Date, mention manuscrite & Signature du Client)</span>
            </div>
            <div class="sig-box">
                <strong>Pour la Direction</strong><br>
                <span style="font-size: 10px; color: #64748b;">(Cachet commercial & Signature autorisée)</span>
            </div>
        </div>
    </div>

</body>
</html>
