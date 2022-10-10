<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * WebsitesHasCompte Entity
 *
 * @property int $Websites_id
 * @property int $Websites_Services_id
 * @property int $Websites_Services_Fournisseurs_id
 * @property int $Websites_Serveurs_id
 * @property int $Websites_Serveurs_Services_id
 * @property int $Websites_Serveurs_Services_Fournisseurs_id
 * @property int $Comptes_id
 * @property int|null $is_root
 * @property string|null $Websites_has_Comptescol
 *
 * @property \App\Model\Entity\Website $website
 * @property \App\Model\Entity\Compte $compte
 */
class WebsitesHasCompte extends Entity
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
        'Websites_has_Comptescol' => true,
        'website' => true,
        'compte' => true,
    ];
}
