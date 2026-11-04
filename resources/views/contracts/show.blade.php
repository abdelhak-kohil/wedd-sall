<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Contrat {{ $reservation->reservation_number }} - {{ $reservation->business->name_fr }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #0f172a; margin: 0; padding: 25px; font-size: 12px; line-height: 1.45; }
        .container { max-width: 820px; margin: 0 auto; border: 2px solid #334155; padding: 35px; border-radius: 8px; background: #ffffff; }
        .rep-header { text-align: center; font-size: 11px; text-transform: uppercase; color: #475569; font-weight: bold; margin-bottom: 12px; letter-spacing: 0.5px; }
        .contract-title-box { text-align: center; border-top: 2px solid #0f172a; border-bottom: 2px solid #0f172a; padding: 10px 0; margin-bottom: 25px; }
        .contract-title { font-size: 18px; font-weight: 900; margin: 0; text-transform: uppercase; color: #0f172a; }
        .contract-title-ar { font-size: 16px; font-weight: bold; margin: 4px 0 0 0; color: #047857; direction: rtl; }
        .res-code { font-family: monospace; font-size: 13px; font-weight: bold; margin-top: 5px; color: #334155; }
        .parties-grid { display: flex; gap: 20px; margin-bottom: 25px; }
        .party-card { flex: 1; border: 1px solid #cbd5e1; padding: 12px 16px; border-radius: 6px; background: #f8fafc; }
        .party-tag { font-size: 10px; font-weight: 800; text-transform: uppercase; color: #047857; margin-bottom: 4px; }
        .party-name { font-size: 13px; font-weight: bold; color: #0f172a; }
        .party-details { font-size: 11px; color: #475569; margin-top: 4px; line-height: 1.4; }
        .article { margin-bottom: 14px; text-align: justify; }
        .article-title { font-weight: 800; font-size: 12px; color: #0f172a; margin-bottom: 3px; }
        .article-title-ar { font-weight: bold; font-size: 12px; color: #047857; direction: rtl; float: right; }
        .article-content { color: #334155; font-size: 11.5px; clear: both; }
        .finance-summary { background: #f1f5f9; border: 1px solid #cbd5e1; padding: 12px; border-radius: 6px; margin: 15px 0; }
        .finance-table { width: 100%; border-collapse: collapse; }
        .finance-table td { padding: 4px 8px; font-size: 12px; }
        .signatures { display: flex; justify-content: space-between; margin-top: 35px; page-break-inside: avoid; }
        .sig-block { width: 45%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px; min-height: 100px; text-align: center; }
        .sig-label { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #475569; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; margin-bottom: 45px; }
        .sig-notice { font-size: 10px; color: #94a3b8; font-style: italic; }
        .no-print-bar { background: #0f172a; color: white; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-radius: 8px; }
        .btn { background: #059669; color: white; border: none; padding: 8px 18px; border-radius: 6px; font-weight: bold; cursor: pointer; text-decoration: none; font-size: 12px; }
        @media print {
            body { padding: 0; font-size: 11px; }
            .container { border: none; padding: 0; max-width: 100%; }
            .no-print-bar { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <span>Contrat Officiel de Location de Salle des Fêtes — Conforme Droit Commercial Algérien</span>
        <div>
            <button onclick="window.print()" class="btn">🖨️ Imprimer le Contrat (2 exemplaires)</button>
            <a href="{{ route('reservations.show', $reservation) }}" style="color: #cbd5e1; margin-left: 15px; text-decoration: none; font-size: 12px;">← Dossier 360°</a>
        </div>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="rep-header">
            République Algérienne Démocratique et Populaire • الجمهورية الجزائرية الديمقراطية الشعبية
        </div>

        <div class="contract-title-box">
            <h1 class="contract-title">CONTRAT DE LOCATION DE SALLE DES FÊTES</h1>
            <div class="contract-title-ar">عقد كراء قاعة حفلات ومناسبات</div>
            <div class="res-code">RÉFÉRENCE : {{ $reservation->reservation_number }}</div>
        </div>

        <!-- Parties -->
        <div class="parties-grid">
            <div class="party-card">
                <div class="party-tag">1. LE BAILLEUR (ÉTABLISSEMENT)</div>
                <div class="party-name">{{ $reservation->business->name_fr }}</div>
                <div class="party-details">
                    @if($reservation->business->address_line){{ $reservation->business->address_line }}, @endif{{ $reservation->business->wilaya }}<br>
                    Tél : {{ $reservation->business->phone }} | Email : {{ $reservation->business->email }}<br>
                    @if($reservation->business->rc)RC : {{ $reservation->business->rc }} | @endif
                    @if($reservation->business->nif)NIF : {{ $reservation->business->nif }} | @endif
                    @if($reservation->business->nis)NIS : {{ $reservation->business->nis }}@endif
                </div>
            </div>

            <div class="party-card">
                <div class="party-tag">2. LE PRENEUR (LE CLIENT)</div>
                <div class="party-name">{{ $reservation->customer_name }}</div>
                <div class="party-details">
                    Téléphone : {{ $reservation->customer_phone }}<br>
                    @if($reservation->customer?->wilaya)Wilaya : {{ $reservation->customer->wilaya->name_fr }} | Commune : {{ $reservation->customer->commune?->name_fr ?? '—' }}<br>@endif
                    @if($reservation->groom_name || $reservation->bride_name)
                        En l'honneur de : <strong>{{ $reservation->groom_name }} & {{ $reservation->bride_name }}</strong><br>
                    @endif
                    Type d'événement : <strong>{{ $reservation->event_type }}</strong>
                </div>
            </div>
        </div>

        <!-- Articles -->
        <div class="article">
            <div class="article-title-ar">المادة 1 : موضوع العقد والقاعة المخصصة</div>
            <div class="article-title">ARTICLE 1 : OBJET & DÉSIGNATION DE LA SALLE</div>
            <div class="article-content">
                Le Bailleur met à disposition exclusive du Preneur l'espace dénommé <strong>{{ $reservation->space->name }}</strong> (situé à l'adresse de l'établissement susmentionné), avec ses installations, éclairages, mobilier (tables, chaises habillées) et commodités, pour l'organisation de l'événement privé ci-après décrit.
            </div>
        </div>

        <div class="article">
            <div class="article-title-ar">المادة 2 : تاريخ ومواقيت الاستغلال</div>
            <div class="article-title">ARTICLE 2 : DATE ET HORAIRES D'EXPLOITATION</div>
            <div class="article-content">
                La mise à disposition est consentie pour la date du <strong>{{ \Carbon\Carbon::parse($reservation->event_date)->translatedFormat('l d F Y') }}</strong>, de <strong>{{ substr($reservation->start_time, 0, 5) }}</strong> à <strong>{{ substr($reservation->end_time, 0, 5) }}</strong>. Le Preneur et ses prestataires s'engagent formellement à respecter ces horaires afin de permettre les opérations d'entretien et d'aération requises.
            </div>
        </div>

        <div class="article">
            <div class="article-title-ar">المادة 3 : عدد المدعوين وطاقة الاستيعاب</div>
            <div class="article-title">ARTICLE 3 : NOMBRE D'INVITÉS & SÉCURITÉ</div>
            <div class="article-content">
                Le nombre d'invités convenu s'élève à <strong>{{ $reservation->expected_guests }} personnes</strong>. Conformément aux règlements administratifs de sécurité, la capacité maximale autorisée de la salle ({{ $reservation->space->capacity_seated }} places) ne pourra être excédée sous aucun prétexte.
            </div>
        </div>

        <div class="article">
            <div class="article-title-ar">المادة 4 : الشروط المالية وطريقة التسديد</div>
            <div class="article-title">ARTICLE 4 : ACCORD FINANCIER & MODALITÉS DE PAIEMENT</div>
            <div class="article-content">
                Le montant total forfaitaire de la présente location est arrêté à la somme de :
                <div class="finance-summary">
                    <table class="finance-table">
                        <tr>
                            <td><strong>Montant total convenu :</strong></td>
                            <td style="text-align: right; font-weight: bold; font-family: monospace; font-size: 13px;">{{ number_format($reservation->total_price, 2, ',', ' ') }} DA</td>
                        </tr>
                        <tr>
                            <td>Acompte exigé à la réservation ('Erboun') :</td>
                            <td style="text-align: right; font-family: monospace;">{{ number_format($reservation->deposit_required, 2, ',', ' ') }} DA</td>
                        </tr>
                        <tr>
                            <td>Acompte effectivement encaissé à ce jour :</td>
                            <td style="text-align: right; font-weight: bold; color: #047857; font-family: monospace;">{{ number_format($reservation->deposit_paid, 2, ',', ' ') }} DA</td>
                        </tr>
                        <tr style="border-top: 1px solid #cbd5e1;">
                            <td><strong>Solde restant dû :</strong></td>
                            <td style="text-align: right; font-weight: bold; font-family: monospace; font-size: 13px; color: {{ $reservation->remaining_balance > 0 ? '#b45309' : '#047857' }};">
                                {{ number_format($reservation->remaining_balance, 2, ',', ' ') }} DA
                            </td>
                        </tr>
                    </table>
                </div>
                Le solde restant dû devra être intégralement réglé par le Preneur au plus tard le jour de l'événement avant l'ouverture des portes.
            </div>
        </div>

        <div class="article">
            <div class="article-title-ar">المادة 5 : شروط الإلغاء والتراجع عن الحجز (العربون)</div>
            <div class="article-title">ARTICLE 5 : DÉSISTEMENT ET ANNULATION (RÈGLE DU 'ERBOUN')</div>
            <div class="article-content">
                Conformément aux dispositions de l'Article 72 bis du Code de Commerce et aux usages constants régissant les salles des fêtes : 
                en cas de désistement ou d'annulation à l'initiative du Preneur, l'acompte ('Erboun') versé reste définitivement acquis au Bailleur à titre de dédommagement forfaitaire pour immobilisation de la date. En cas de force majeure légalement constatée, un report de date pourra être convenu selon le calendrier disponible.
            </div>
        </div>

        <div class="article">
            <div class="article-title-ar">المادة 6 : النظام العام، السكينة والسلامة</div>
            <div class="article-title">ARTICLE 6 : TRANQUILLITÉ PUBLIQUE, NUISANCES & SÉCURITÉ</div>
            <div class="article-content">
                Le Preneur s'engage à veiller au bon comportement de ses invités. Sont expressément interdits : les tirs d'armes à feu (Baroud sans autorisation préfectorale), l'usage de feux d'artifice à l'intérieur, et le stationnement gênant sur la voie publique. Le volume sonore doit être ramené aux normes réglementaires après minuit afin de préserver la tranquillité du voisinage.
            </div>
        </div>

        <div class="article">
            <div class="article-title-ar">المادة 7 : المسؤولية عن الإتلافات والأضرار</div>
            <div class="article-title">ARTICLE 7 : RESPONSABILITÉ & DÉGRADATIONS MATÉRIELLES</div>
            <div class="article-content">
                Le Preneur est pécuniairement responsable de toute détérioration, casse ou perte constatée sur le matériel, la lustrerie, la sonorisation ou les locaux durant la période d'occupation et s'engage à en rembourser la valeur à première demande.
            </div>
        </div>

        <!-- Mentions finales & Signatures -->
        <div style="font-size: 11px; color: #475569; margin-top: 20px; text-align: center;">
            Fait à <strong>{{ $reservation->business->wilaya }}</strong>, le {{ now()->translatedFormat('d F Y') }}, en deux (02) exemplaires originaux de bonne foi.
        </div>

        <div class="signatures">
            <div class="sig-block">
                <div class="sig-label">LE PRENEUR (LE CLIENT)</div>
                <div class="sig-notice">Mention manuscrite "Lu et approuvé"<br>suivie de la signature</div>
            </div>
            <div class="sig-block">
                <div class="sig-label">POUR LA SALLE DES FÊTES</div>
                <div class="sig-notice">Signature de la direction<br>& Cachet officiel de l'établissement</div>
            </div>
        </div>
    </div>

</body>
</html>
