<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTask extends Model
{
    public const STATUS_PENDING     = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED   = 'completed';

    public const CATEGORY_SALLE      = 'salle_mobilier';
    public const CATEGORY_DECO       = 'decoration';
    public const CATEGORY_TECH       = 'technique';
    public const CATEGORY_TRAITEUR   = 'traiteur';
    public const CATEGORY_ACCUEIL    = 'accueil_securite';
    public const CATEGORY_CLOTURE    = 'cloture_nettoyage';

    public const CATEGORIES = [
        self::CATEGORY_SALLE    => ['label' => 'Salle & Mobilier',      'icon' => '🪑', 'color' => 'amber'],
        self::CATEGORY_DECO     => ['label' => 'Décoration & Scène',    'icon' => '🌸', 'color' => 'rose'],
        self::CATEGORY_TECH     => ['label' => 'Son, Lumière & Écrans', 'icon' => '💡', 'color' => 'sky'],
        self::CATEGORY_TRAITEUR => ['label' => 'Traiteur & Boissons',   'icon' => '🍽️', 'color' => 'emerald'],
        self::CATEGORY_ACCUEIL  => ['label' => 'Accueil & Sécurité',    'icon' => '🛡️', 'color' => 'purple'],
        self::CATEGORY_CLOTURE  => ['label' => 'Clôture & Nettoyage',   'icon' => '🧹', 'color' => 'slate'],
    ];

    public const TIMING_PHASES = [
        'J_MINUS_2'     => ['label' => 'J-2 (Avant-veille)',            'color' => 'slate'],
        'J_MINUS_1'     => ['label' => 'J-1 (Veille)',                  'color' => 'blue'],
        'DAY_OF_SETUP'  => ['label' => 'Jour J (Installation & Prêt)',  'color' => 'emerald'],
        'DURING_EVENT'  => ['label' => 'Pendant la fête',               'color' => 'amber'],
        'POST_EVENT'    => ['label' => 'Clôture / Après la fête',       'color' => 'zinc'],
    ];

    protected $fillable = [
        'business_id',
        'reservation_id',
        'title',
        'category',
        'timing_phase',
        'status',
        'is_critical',
        'assigned_to',
        'completed_at',
        'completed_by',
        'notes',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_critical'  => 'boolean',
            'completed_at' => 'datetime',
            'sort_order'   => 'integer',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function completedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // ── Computed attributes ────────────────────────────────────────────────────

    public function getCategoryMetaAttribute(): array
    {
        return self::CATEGORIES[$this->category] ?? ['label' => $this->category, 'icon' => '📋', 'color' => 'slate'];
    }

    public function getTimingMetaAttribute(): array
    {
        return self::TIMING_PHASES[$this->timing_phase] ?? ['label' => $this->timing_phase, 'color' => 'slate'];
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    // ── Pre-packaged templates ─────────────────────────────────────────────────

    /**
     * Generate standard Algerian wedding / event operational checklist
     */
    public static function generateDefaultChecklist(Reservation $reservation, ?int $userId = null): int
    {
        $templates = [
            // J-1
            [
                'title'        => 'Nettoyage approfondi de la salle, sanitaires et office traiteur',
                'category'     => self::CATEGORY_CLOTURE,
                'timing_phase' => 'J_MINUS_1',
                'is_critical'  => true,
            ],
            [
                'title'        => 'Disposition et alignement des tables rondes et chaises selon plan de salle',
                'category'     => self::CATEGORY_SALLE,
                'timing_phase' => 'J_MINUS_1',
                'is_critical'  => true,
            ],
            [
                'title'        => 'Repassage et mise en place des nappes satin et housses/galettes de chaises',
                'category'     => self::CATEGORY_SALLE,
                'timing_phase' => 'J_MINUS_1',
                'is_critical'  => false,
            ],
            [
                'title'        => 'Installation de l\'estrade d\'honneur, Kosha (trône des mariés) et arche',
                'category'     => self::CATEGORY_DECO,
                'timing_phase' => 'J_MINUS_1',
                'is_critical'  => true,
            ],

            // Jour J - Matin & Installation
            [
                'title'        => 'Test complet sonorisation, micros HF (piles neuves) et jeux de lumières / PAR LED',
                'category'     => self::CATEGORY_TECH,
                'timing_phase' => 'DAY_OF_SETUP',
                'is_critical'  => true,
            ],
            [
                'title'        => 'Vérification groupe électrogène / secours électrique et réserves de carburant',
                'category'     => self::CATEGORY_TECH,
                'timing_phase' => 'DAY_OF_SETUP',
                'is_critical'  => true,
            ],
            [
                'title'        => 'Vérification et allumage de la climatisation / chauffage (T° idéale 22°C)',
                'category'     => self::CATEGORY_TECH,
                'timing_phase' => 'DAY_OF_SETUP',
                'is_critical'  => true,
            ],
            [
                'title'        => 'Arrivée du traiteur : contrôle propreté cuisine, armoires réfrigérées et accès',
                'category'     => self::CATEGORY_TRAITEUR,
                'timing_phase' => 'DAY_OF_SETUP',
                'is_critical'  => true,
            ],
            [
                'title'        => 'Mise en place de la vaisselle, verres cristal et couverts sur les tables',
                'category'     => self::CATEGORY_TRAITEUR,
                'timing_phase' => 'DAY_OF_SETUP',
                'is_critical'  => false,
            ],
            [
                'title'        => 'Installation des centres de table (candélabres dorés / compositions florales)',
                'category'     => self::CATEGORY_DECO,
                'timing_phase' => 'DAY_OF_SETUP',
                'is_critical'  => false,
            ],

            // Pendant la fête
            [
                'title'        => 'Accueil et briefing des agents de sécurité & hôtesse vestiaire',
                'category'     => self::CATEGORY_ACCUEIL,
                'timing_phase' => 'DURING_EVENT',
                'is_critical'  => true,
            ],
            [
                'title'        => 'Contrôle permanent de la propreté et du réapprovisionnement des sanitaires',
                'category'     => self::CATEGORY_ACCUEIL,
                'timing_phase' => 'DURING_EVENT',
                'is_critical'  => true,
            ],
            [
                'title'        => 'Coordination arrivée du cortège mariés avec le DJ et machine à étincelles / fumée',
                'category'     => self::CATEGORY_TECH,
                'timing_phase' => 'DURING_EVENT',
                'is_critical'  => false,
            ],

            // Clôture
            [
                'title'        => 'Règlement du solde restant auprès du responsable de famille avant le gâteau',
                'category'     => self::CATEGORY_ACCUEIL,
                'timing_phase' => 'DURING_EVENT',
                'is_critical'  => true,
            ],
            [
                'title'        => 'Comptage retour du matériel loué (vaisselle, nappes, sono) et état des lieux sortie',
                'category'     => self::CATEGORY_CLOTURE,
                'timing_phase' => 'POST_EVENT',
                'is_critical'  => true,
            ],
        ];

        $count = 0;
        foreach ($templates as $i => $tpl) {
            // Avoid duplicate creation
            self::firstOrCreate(
                [
                    'business_id'    => $reservation->business_id,
                    'reservation_id' => $reservation->id,
                    'title'          => $tpl['title'],
                ],
                [
                    'category'     => $tpl['category'],
                    'timing_phase' => $tpl['timing_phase'],
                    'status'       => self::STATUS_PENDING,
                    'is_critical'  => $tpl['is_critical'],
                    'sort_order'   => $i + 1,
                    'assigned_to'  => $userId,
                ]
            );
            $count++;
        }

        return $count;
    }
}
