<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Transport Entity
 *
 * @property int $id
 * @property string $nom_contact
 * @property string $num_client
 * @property string $tel_contact
 * @property int $agence_depart_aller_id
 * @property int $agence_arrivee_aller_id
 * @property int $agence_depart_retour_id
 * @property int $agence_arrivee_retour_id
 * @property string $materiel
 * @property string $description_probleme
 * @property \Cake\I18n\FrozenDate $date_depot
 * @property int $quantité
 * @property int $nb_colis
 * @property int $etat
 * @property int $transport
 * @property \Cake\I18n\FrozenDate $date_reception
 * @property \Cake\I18n\FrozenDate $date_retour
 * @property int $intervention_id
 *
 * @property \App\Model\Entity\Agence $agence
 * @property \App\Model\Entity\Intervention $intervention
 */
class Transport extends Entity {

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'nom_contact' => true,
        'num_client' => true,
        'tel_contact' => true,
        'email_contact' => true,
        'agence_depart_aller_id' => true,
        'agence_arrivee_aller_id' => true,
        'agence_depart_retour_id' => true,
        'agence_arrivee_retour_id' => true,
        'contenu' => true,
        'motif' => true,
        'date_depot' => true,
        'nb_colis' => true,
        'etat' => true,
        'date_depart_aller' => true,
        'date_arrivee_aller' => true,
        'date_depart_retour' => true,
        'date_arrivee_retour' => true,
        'intervention_id' => true,
        'expediteur' => true,
        'agence' => true,
    ];

}
