# Phase 3: UI/UX Architecture & Page Specifications
**Project:** Algerian Wedding Hall Management System (*Système de Gestion de Salle des Fêtes*)  
**Design Philosophy:** Minimalist, fast, wedding-inspired luxury (neutral slate/zinc backgrounds, deep emerald accents, gold badges, high-contrast typography, zero clutter).  
**Localization:** Dual-language engine (French / Arabic with native `dir="rtl"` support, Tajawal/Cairo fonts for Arabic, Inter for Latin).

---

## 1. Global Navigation & Layout Architecture

### 1.1 Responsive Desktop Shell (Reception & Management)

```
+----------------------------------------------------------------------------------------------------+
|  [Logo] PALAIS EL BAHADJA  | [Search Customers, Res #, Phone... (Ctrl+K)] | [Alt+A Disponibilité]  | [FR | ع] [User] |
+----------------------------+-----------------------------------------------------------------------+
| SIDEBAR                    | MAIN CONTENT VIEW AREA                                                |
|                            |                                                                       |
| [DASHBOARD]                |                                                                       |
| [DISPONIBILITÉ RAPIDE] ★   |                                                                       |
| [CALENDRIER]               |                                                                       |
| [RÉSERVATIONS]             |                                                                       |
| [DEVIS / QUOTATIONS]       |                                                                       |
| [CLIENTS & PROSPECTS]      |                                                                       |
| -------------------------- |                                                                       |
| OPÉRATIONS & LOGISTIQUE    |                                                                       |
|  - Fiches Événements       |                                                                       |
|  - Checklists du Jour      |                                                                       |
|  - Préparation Cuisine     |                                                                       |
|  - Personnel & Extras      |                                                                       |
| -------------------------- |                                                                       |
| FINANCES                   |                                                                       |
|  - Paiements & Reçus       |                                                                       |
|  - Caisse Journalière      |                                                                       |
|  - Dépenses & Marges       |                                                                       |
| -------------------------- |                                                                       |
| CONFIGURATION              |                                                                       |
|  - Salles & Horaires       |                                                                       |
|  - Forfaits & Menus        |                                                                       |
|  - Paramètres Entreprise   |                                                                       |
+----------------------------+-----------------------------------------------------------------------+
```

### 1.2 Mobile / Tablet Viewport (Floor Coordinator & Hall Tours)
- **Top Bar:** Business logo, active space badge, Language toggle, WhatsApp quick launcher.
- **Bottom Navigation Bar (5 core actions):**
  1. `Aujourd'hui` (Today's run of show & alerts)
  2. `Dispo` (Instant date/slot availability picker)
  3. `+ Réserver` (Floating primary action button)
  4. `Calendrier` (Mobile month/agenda view)
  5. `Menu` (Slide-out drawer for clients, finances, settings)

---

## 2. Complete Page & Route Inventory

| Route | View Name | Roles Allowed | Description & Primary Actions |
| :--- | :--- | :--- | :--- |
| `/login` | Authentication | All | Clean bilingual credentials entry with remember-me. |
| `/dashboard` | Operational Dashboard | All (role-scoped) | Today's weddings, critical payment alerts, expiring holds, monthly revenue/profit KPIs. |
| `/availability` | Quick Availability Grid | All | Ultra-fast (< 3s) single-date space & slot inspector for phone inquiries. |
| `/calendar` | Booking Calendar | All | Interactive Month / Week / Day / Agenda view. Color-coded booking chips. |
| `/reservations` | Reservations Directory | All | Filterable table by status, space, date range, payment status. CSV export. |
| `/reservations/create` | Quick Reservation Flow | Owner, Manager, Receptionist | Step-by-step modal/page completed in under 60 seconds. |
| `/reservations/{id}` | Event 360 File (*Fiche Événement*) | All (sections scoped) | The single source of truth for a wedding: bride/groom, menu, staff, checklists, financial balance, contract. |
| `/quotations` | Devis / Quotes | Owner, Manager, Receptionist | Draft, customize line items, generate printable PDF with Algerian legal headers. |
| `/customers` | Client CRM | Owner, Manager, Receptionist | Customer dossiers, wilaya/commune filter, direct WhatsApp message links. |
| `/leads` | Prospect Funnel | Owner, Manager, Receptionist | Kanban/list of inquiries (Visit Scheduled, Visited, Lost with reasons). |
| `/payments` | Payments Ledger | Owner, Manager, Accountant | List of transactions, receipt reprints, filter by payment method. |
| `/payments/cash-register` | Daily Cash Register | Owner, Accountant | Opening balance, daily inflows/outflows, cash reconciliation, end-of-day lock. |
| `/operations/today` | Wedding Day Control Center | Owner, Manager, Coordinator | Real-time run of show, kitchen food prep quantities, coordinator checklist. |
| `/operations/staff` | Extra Staff & Roster | Owner, Manager, Coordinator | Assign waiters/security, track hourly/event flat rates, record staff payout. |
| `/finances/profitability` | Event Net Profitability | Owner, Accountant | Per-wedding revenue vs expenses, gross and net profit margin breakdown. |
| `/finances/expenses` | Expense Journal | Owner, Accountant | Record direct wedding expenses & general hall overheads. |
| `/settings/spaces` | Spaces & Time Slots | Owner, Manager | Configure physical halls (Royale, Andalouse), capacities, and operational time slots. |
| `/settings/packages` | Package & Menu Catalog | Owner, Manager | Configure fixed/per-guest pricing tiers, included equipment, menu courses. |
| `/settings/contracts` | Contract Templates | Owner, Manager | Edit standard legal clauses, payment deadlines, damage policies with token replacement. |
| `/settings/business` | Company Identity | Owner | Hall name (FR/AR), NIF/NIS/RC, address, logo, currency (`DA`), timezone (`Africa/Algiers`). |

---

## 3. High-Fidelity Wireframe Specifications

### 3.1 The 5-Second Phone Availability Checker (`/availability`)
**Objective:** Receptionist receives a call: *"Is Saturday, 18 July available for a wedding in Salle Royale?"*

```
+----------------------------------------------------------------------------------------------------+
| DISPONIBILITÉ IMMÉDIATE (Date Picker: [ < ] [ Samedi 18 Juillet 2026 ] [ > ] [ Aujourd'hui ] )     |
+----------------------------------------------------------------------------------------------------+
| FILTRES RAPIDES: [ Toutes les salles ]  [ Minimum 300 personnes ]  [ Soirée ]                     |
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|  SALLE ROYALE (Capacité: 150 - 450 personnes)                                                      |
|  +-------------------------------------+---------------------------------------------------------+ |
|  | CRÉNEAU APRÈS-MIDI (14:00 - 18:00)  | CRÉNEAU SOIRÉE (19:00 - 02:00)                          | |
|  | [ DISPONIBLE ]                      | [ RÉSERVÉ - CONFIRMÉ ]                                  | |
|  | Tarif de base: 140,000 DA           | Mariage: Karim B. & Sarah M. (320 invités)              | |
|  |                                     | Solde restant: 120,000 DA                               | |
|  | [+ Bloquer cette date]              | [Voir Fiche Événement]                                  | |
|  +-------------------------------------+---------------------------------------------------------+ |
|                                                                                                    |
|  SALLE ANDALOUSE (Capacité: 80 - 250 personnes)                                                    |
|  +-------------------------------------+---------------------------------------------------------+ |
|  | CRÉNEAU APRÈS-MIDI (14:00 - 18:00)  | CRÉNEAU SOIRÉE (19:00 - 02:00)                          | |
|  | [ OPTION TEMPORAIRE ]               | [ DISPONIBLE ]                                          | |
|  | Réservé pour: Amel T.               | Tarif de base: 180,000 DA                               | |
|  | Expire dans: 14 heures (Demain 18h) |                                                         | |
|  | [+ Convertir]  [+ Relancer WhatsApp]| [+ Bloquer cette date]                                  | |
|  +-------------------------------------+---------------------------------------------------------+ |
|                                                                                                    |
+----------------------------------------------------------------------------------------------------+
```

---

### 3.2 The Fast Reservation Modal (< 60 Seconds)
**Triggered by:** Clicking `[+ Bloquer cette date]` or `+ Nouvelle Réservation`.

```
+----------------------------------------------------------------------------------------------------+
|  NOUVELLE RÉSERVATION (Salle Royale — Samedi 18 Juillet 2026 — Soirée 19:00-02:00)                 |
+----------------------------------------------------------------------------------------------------+
|  1. CLIENT                                                                                         |
|     Numéro de Téléphone: [ 0550 12 34 56        ] -> Trouvé: Mohamed Belkacem (Alger Centre)      |
|     Nom de la Mariée:    [ Yasmina               ]   Nom du Marié: [ Mohamed                    ]  |
|                                                                                                    |
|  2. DÉTAILS DE L'ÉVÉNEMENT                                                                         |
|     Type d'événement:    (o) Mariage   ( ) Fiançailles   ( ) Henné   ( ) Autre                     |
|     Nombre d'invités:    [ 300   ] personnes                                                       |
|     Formule / Forfait:   [ Forfait Gold (Salle + Déco + DJ + Serveurs)            |v]               |
|                                                                                                    |
|  3. TARIFICATION                                                                                   |
|     Montant Total Fixé:  [ 450,000 DA   ]     Remise accordée: [ 0 DA        ]                     |
|     Acompte exigé:       [ 100,000 DA   ]     Date limite solde: [ 11/07/2026 ]                    |
|                                                                                                    |
|  4. TYPE DE VALIDATION                                                                             |
|     [ ] OPTION TEMPORAIRE (Sans paiement - Expire sous 48h)                                        |
|     [X] CONFIRMATION AVEC ACOMPTE ENCAISSÉ                                                         |
|         Montant versé aujourd'hui: [ 100,000 DA ]                                                  |
|         Mode de versement:         (o) Espèces   ( ) Virement CCP/BaridiMob   ( ) Chèque           |
|                                                                                                    |
+----------------------------------------------------------------------------------------------------+
|  [ Annuler ]                                            [ ENREGISTRER & IMPRIMER LE REÇU PDF (F2) ]|
+----------------------------------------------------------------------------------------------------+
```

---

### 3.3 Event 360 File (*Fiche Événement*)
**The single master screen** accessible to owner, manager, coordinator, and accountant:

```
+----------------------------------------------------------------------------------------------------+
| RES-2026-0042: Mariage Mohamed & Yasmina                       [Badge: CONFIRMÉ] [Badge: ACOMPTE PAYÉ] |
| Date: Samedi 18 Juillet 2026 (19:00 - 02:00) | Salle: Royale (300 invités)   [Imprimer Contrat PDF] |
+----------------------------------------------------------------------------------------------------+
| [Onglet 1: Vue Globale] [Onglet 2: Finances] [Onglet 3: Menu & Cuisine] [Onglet 4: Staff & Fournisseurs]
+----------------------------------------------------------------------------------------------------+
| RÉSUMÉ FINANCIER RAPIDE              | ACTIONS DIRECTES WHATSAPP                                   |
| - Prix convenu:       450,000 DA     | [ Message Confirmation ]  [ Rappel Solde Restant ]          |
| - Acompte payé:       100,000 DA     | Téléphone: +213 550 12 34 56                                |
| - Solde restant:      350,000 DA     |                                                             |
| - Statut:     77% non encore réglé   | COORDINATEUR DÉSIGNÉ: Sara (Responsable de salle)           |
+--------------------------------------+-------------------------------------------------------------+
| FEUILLE DE ROUTE LOGISTIQUE (CHECKLIST)                                                            |
|  [X] Contrat signé & archivé (Reçu le 12/03)                                                        |
|  [X] Acompte de 100,000 DA encaissé (Reçu #REC-0129)                                               |
|  [ ] Confirmation définitive du nombre de tables et chaises (Échéance: 11 Juillet)                 |
|  [ ] Validation finale du menu avec le traiteur / chef cuisinier (Échéance: 14 Juillet)             |
|  [ ] Règlement du solde restant de 350,000 DA (Échéance: 11 Juillet - Dans 14 jours)               |
+----------------------------------------------------------------------------------------------------+
| COMPOSITION DU MENU                                | ÉQUIPE & SERVICES AFFECTÉS                   |
| - Entrée: Chorba Frik + Bourek Traditionnel        | - Chef de cuisine: Amine (12,000 DA)          |
| - Plat: Tajine Zitoun + Viande Rôtie               | - Serveurs (8): Forfait 24,000 DA             |
| - Dessert: Salade de fruits frais + Pâtisseries    | - Sécurité (2): Forfait 8,000 DA              |
| - Boissons: Eau minérale, Jus, Café & Thé          | - DJ & Sonorisation: DJ Karim (30,000 DA)     |
+----------------------------------------------------+-----------------------------------------------+
| CALCUL DE RENTABILITÉ ESTIMATIVE                                                                   |
| Chiffre d'Affaires: 450,000 DA  -  Coûts Directs: 185,000 DA  =  Bénéfice Net Estimé: 265,000 DA  |
| Marge nette prévisionnelle: 58.8%                                                                  |
+----------------------------------------------------------------------------------------------------+
```

---

## 4. Bilingual Design System & RTL Handling

1. **Direction Switching:**
   - When Arabic is active, `html[dir="rtl"]` triggers inverted flexbox/grid margins, mirroring the sidebar to the right.
   - Arabic font: `Tajawal` / `Cairo` with enhanced line-height for readability.
   - Numbers & Currency: Formatted consistently as `450 000 DA` (or `450,000 دج`).
2. **Color Semantics:**
   - **Confirmed / Fully Paid:** `#059669` (Emerald Green)
   - **Temporary Hold / Deposit Pending:** `#D97706` (Amber Warm)
   - **Overdue Payment / Overlap Hazard:** `#DC2626` (Ruby Red)
   - **Inquiry / Draft:** `#4B5563` (Neutral Slate)
   - **Luxury Accent:** Deep Navy `#0F172A` with warm champagne borders `#E2D9C8`.
