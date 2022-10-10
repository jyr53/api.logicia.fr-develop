<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Sauvegarde Entity
 *
 * @property int $id
 * @property int $Services_id
 * @property int $Services_Fournisseurs_id
 * @property int $Comptes_id
 * @property \Cake\I18n\FrozenTime|null $derniere
 * @property string|resource|null $commentaire
 * @property string|null $frequence
 *
 * @property \App\Model\Entity\Service $service
 * @property \App\Model\Entity\Compte $compte
 */
class Sauvegarde extends Entity {

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
        'Services_id' => true,
        'Services_Fournisseurs_id' => true,
        'Comptes_id' => true,
        'derniere' => true,
        'commentaire' => true,
        'frequence' => true,
        'service' => true,
        'service_fournisseur' => true,
        'compte' => true,
    ];

}
