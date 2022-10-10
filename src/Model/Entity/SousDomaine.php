<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SousDomaine Entity
 *
 * @property int $id
 * @property int $Domaines_id
 * @property int $Domaines_Services_id
 * @property int $Domaines_Services_Fournisseurs_id
 * @property int $Websites_id
 * @property int $Websites_Services_id
 * @property int $Websites_Services_Fournisseurs_id
 * @property string|null $nom
 *
 * @property \App\Model\Entity\Domaine $domaine
 * @property \App\Model\Entity\Website $website
 */
class SousDomaine extends Entity
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
        'nom' => true,
        'domaine' => true,
        'website' => true,
    ];
}
