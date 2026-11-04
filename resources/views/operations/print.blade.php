<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feuille de Route Jour J - {{ $reservation->reservation_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            font-size: 11pt;
            line-height: 1.4;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 32px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18pt;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        .header p {
            font-size: 9pt;
            color: #64748b;
        }
        .badge-ref {
            display: inline-block;
            font-family: 'JetBrains Mono', monospace;
            background: #f1f5f9;
            color: #0f172a;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 10pt;
            border: 1px solid #cbd5e1;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px;
            margin-bottom: 24px;
        }
        .summary-item .label {
            font-size: 8pt;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.5px;
        }
        .summary-item .value {
            font-size: 11pt;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2px;
        }
        .section-title {
            font-size: 12pt;
            font-weight: 800;
            color: #0f172a;
            border-left: 4px solid #4f46e5;
            padding-left: 8px;
            margin-top: 24px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
            margin-bottom: 16px;
        }
        th {
            background: #f1f5f9;
            color: #334155;
            text-align: left;
            font-weight: 700;
            padding: 8px 10px;
            border-bottom: 1px solid #cbd5e1;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        tr:nth-child(even) td {
            background-color: #fafbfc;
        }
        .time-cell {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            color: #4f46e5;
            white-space: nowrap;
        }
        .critical-badge {
            background: #fee2e2;
            color: #991b1b;
            font-size: 7.5pt;
            font-weight: 700;
            padding: 2px 5px;
            border-radius: 3px;
            display: inline-block;
        }
        .checkbox-box {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 1.5px solid #64748b;
            border-radius: 3px;
            vertical-align: middle;
            margin-right: 6px;
        }
        .checkbox-box.checked {
            background: #0f172a;
            position: relative;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 36px;
            padding-top: 16px;
            border-top: 1px dashed #cbd5e1;
        }
        .sig-box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px;
            height: 100px;
            font-size: 9pt;
            color: #475569;
        }
        .sig-box strong {
            display: block;
            margin-bottom: 4px;
            color: #0f172a;
        }
        .actions-bar {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 100;
        }
        .btn {
            background: #0f172a;
            color: #ffffff;
            border: none;
            padding: 10px 18px;
            font-size: 10pt;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);
            text-decoration: none;
        }
        .btn-secondary {
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #cbd5e1;
        }
        @media print {
            body { background: #ffffff; padding: 0; }
            .container { border: none; box-shadow: none; padding: 0; max-width: 100%; }
            .actions-bar { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="actions-bar">
        <a href="{{ route('operations.show', $reservation) }}" class="btn btn-secondary">← Retour au Command Center</a>
        <button onclick="window.print()" class="btn">🖨️ Imprimer la Feuille Jour J</button>
    </div>

    <div class="container">
        
        {{-- Header --}}
        <div class="header">
            <div>
                <h1>{{ $business->name_fr ?? 'Palais des Fêtes' }}</h1>
                <p>{{ $business->address_line ?? 'Alger' }} · Tél: {{ $business->phone ?? '0550 12 34 56' }}</p>
                <p style="margin-top: 4px; font-weight: 700; color: #4f46e5; font-size: 11pt;">
                    FEUILLE DE ROUTE OPÉRATIONNELLE & BRIEFING STAFF — JOUR J
                </p>
            </div>
            <div style="text-align: right;">
                <div class="badge-ref">{{ $reservation->reservation_number }}</div>
                <p style="margin-top: 4px;">Édité le {{ now()->format('d/m/Y à H:i') }}</p>
            </div>
        </div>

        {{-- Event Summary Grid --}}
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">Date de l'événement</div>
                <div class="value">{{ $reservation->event_date->translatedFormat('l d F Y') }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Salle & Créneau</div>
                <div class="value">{{ $reservation->space->name_fr ?? 'Salle' }} ({{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }})</div>
            </div>
            <div class="summary-item">
                <div class="label">Type de Fête & Invités</div>
                <div class="value">{{ $reservation->event_type }} · {{ $reservation->expected_guests }} pers.</div>
            </div>
            <div class="summary-item">
                <div class="label">Mariés</div>
                <div class="value" style="color: #b45309;">
                    {{ $reservation->groom_name ?: '—' }} & {{ $reservation->bride_name ?: '—' }}
                </div>
            </div>
            <div class="summary-item">
                <div class="label">Contact Responsable Client</div>
                <div class="value">{{ $reservation->customer_name }} ({{ $reservation->customer_phone }})</div>
            </div>
            <div class="summary-item">
                <div class="label">Solde restant à régler</div>
                <div class="value" style="{{ $reservation->remaining_balance > 0 ? 'color: #b91c1c;' : 'color: #15803d;' }}">
                    {{ number_format($reservation->remaining_balance, 0, ',', ' ') }} DA
                    @if($reservation->remaining_balance > 0)
                        (À percevoir ce soir)
                    @else
                        (Intégralement réglé)
                    @endif
                </div>
            </div>
        </div>

        {{-- 1. Day of Timetable (Run of Show) --}}
        <h2 class="section-title">1. Déroulement Chronologique (Run of Show)</h2>
        @if($reservation->timelines->isEmpty())
            <p style="font-size: 9pt; color: #64748b; font-style: italic; margin-bottom: 14px;">Aucun horaire spécifique enregistré.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">Horaire</th>
                        <th style="width: 250px;">Étape / Action</th>
                        <th>Détails & Consignes</th>
                        <th style="width: 140px;">Responsable</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservation->timelines as $step)
                    <tr>
                        <td class="time-cell">{{ $step->time }}</td>
                        <td><strong>{{ $step->title }}</strong></td>
                        <td style="color: #475569;">{{ $step->description ?: '—' }}</td>
                        <td>{{ $step->person_in_charge ?: 'Staff salle' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- 2. External Vendors --}}
        <h2 class="section-title">2. Prestataires Externes Déclarés</h2>
        @if($reservation->vendors->isEmpty())
            <p style="font-size: 9pt; color: #64748b; font-style: italic; margin-bottom: 14px;">Aucun prestataire externe enregistré.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 140px;">Prestation</th>
                        <th>Entreprise / Contact</th>
                        <th style="width: 120px;">Téléphone</th>
                        <th style="width: 90px;">Arrivée</th>
                        <th>Besoins particuliers</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservation->vendors as $vendor)
                    @php $vCat = $vendor->category_meta; @endphp
                    <tr>
                        <td><strong>{{ $vCat['label'] }}</strong></td>
                        <td>
                            {{ $vendor->company_name }}
                            @if($vendor->contact_person)
                                <div style="font-size: 8.5pt; color: #64748b;">Contact: {{ $vendor->contact_person }}</div>
                            @endif
                        </td>
                        <td style="font-family: 'JetBrains Mono', monospace; font-size: 9pt;">{{ $vendor->phone }}</td>
                        <td class="time-cell">{{ $vendor->arrival_time ?: '—' }}</td>
                        <td style="font-size: 8.5pt; color: #475569;">{{ $vendor->special_notes ?: 'Standard' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- 3. Preparation Checklist --}}
        <h2 class="section-title">3. Points de Contrôle & Conformité Salle (Checklist)</h2>
        @if($reservation->tasks->isEmpty())
            <p style="font-size: 9pt; color: #64748b; font-style: italic; margin-bottom: 14px;">Aucun point de contrôle enregistré.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">État</th>
                        <th>Point de contrôle / Tâche</th>
                        <th style="width: 120px;">Moment</th>
                        <th style="width: 100px;">Caractère</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservation->tasks as $task)
                    <tr>
                        <td style="text-align: center;">
                            <span class="checkbox-box {{ $task->is_completed ? 'checked' : '' }}"></span>
                        </td>
                        <td>
                            <strong>{{ $task->title }}</strong>
                            @if($task->notes)
                                <div style="font-size: 8.5pt; color: #64748b;">Note: {{ $task->notes }}</div>
                            @endif
                        </td>
                        <td style="font-size: 8.5pt; color: #64748b;">
                            {{ $task->timing_meta['label'] }}
                        </td>
                        <td>
                            @if($task->is_critical)
                                <span class="critical-badge">OBLIGATOIRE</span>
                            @else
                                <span style="font-size: 8pt; color: #64748b;">Standard</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Signatures --}}
        <div class="signatures">
            <div class="sig-box">
                <strong>Le Régisseur / Coordinateur de la Salle :</strong>
                Visa & Validation de conformité avant ouverture des portes :
            </div>
            <div class="sig-box">
                <strong>Le Responsable de Famille (Client) :</strong>
                Bon pour accord déroulement & mise à disposition de la salle :
            </div>
        </div>

    </div>

</body>
</html>
