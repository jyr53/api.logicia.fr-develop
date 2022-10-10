<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ServeursHasCompte Entity
 *
 * @property int $Serveurs_id
 * @property int $Serveurs_Services_id
 * @property int $Serveurs_Services_Fournisseurs_id
 * @property int $Comptes_id
 * @property int|null $is_root
 *
 * @property \App\Model\Entity\Serveur $serveur
 * @property \App\Model\Entity\Compte $compte
 */
class ServeursHasCompte extends Entity
{
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
        'is_root' => true,
        'serveur' => true,
        'compte' => true,
    ];
}
