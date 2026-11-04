<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTimeline extends Model
{
    protected $fillable = [
        'business_id',
        'reservation_id',
        'time',
        'title',
        'description',
        'person_in_charge',
        'is_done',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_done'    => 'boolean',
            'sort_order' => 'integer',
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

    /**
     * Generate standard timeline milestones for an evening wedding / event
     */
    public static function generateDefaultTimeline(Reservation $reservation): int
    {
        $defaultSteps = [
            [
                'time'             => '15:00',
                'title'            => 'Accès salle pour Décorateur, DJ et Traiteur',
                'description'      => 'Installation matériel son, vérification prises et mise en chauffe cuisines',
                'person_in_charge' => 'Régisseur de salle',
            ],
            [
                'time'             => '17:30',
                'title'            => 'Arrivée de la famille proche et vérification finale',
                'description'      => 'Accueil des parents, remise des clés loge mariés et consignes',
                'person_in_charge' => 'Coordinatrice',
            ],
            [
                'time'             => '18:30',
                'title'            => 'Ouverture des portes aux invités & Service thé/gâteaux',
                'description'      => 'Accueil à l\'entrée, orientation vers les tables et musique d\'ambiance douce',
                'person_in_charge' => 'Équipe accueil & Serveurs',
            ],
            [
                'time'             => '20:30',
                'title'            => 'Entrée triomphale du cortège & Mariés (Dakhla)',
                'description'      => 'Activation fumée lourde, étincelles froides et musique d\'entrée DJ',
                'person_in_charge' => 'DJ & Régie technique',
            ],
            [
                'time'             => '21:45',
                'title'            => 'Service du Dîner principal',
                'description'      => 'Service aux tables ou ouverture des buffets chauds',
                'person_in_charge' => 'Chef Traiteur',
            ],
            [
                'time'             => '23:30',
                'title'            => 'Cérémonie de la Pièce Montée (Gâteau d\'honneur)',
                'description'      => 'Changement de tenue des mariés, musique gâteau et séance photos',
                'person_in_charge' => 'Photographe & DJ',
            ],
            [
                'time'             => '01:30',
                'title'            => 'Fin de soirée & Départ des invités',
                'description'      => 'Baisse progressive de la musique et fermeture des accès extérieurs',
                'person_in_charge' => 'Sécurité',
            ],
        ];

        $count = 0;
        foreach ($defaultSteps as $i => $step) {
            self::firstOrCreate(
                [
                    'business_id'    => $reservation->business_id,
                    'reservation_id' => $reservation->id,
                    'title'          => $step['title'],
                ],
                [
                    'time'             => $step['time'],
                    'description'      => $step['description'],
                    'person_in_charge' => $step['person_in_charge'],
                    'is_done'          => false,
                    'sort_order'       => $i + 1,
                ]
            );
            $count++;
        }

        return $count;
    }
}
