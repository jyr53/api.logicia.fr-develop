<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Serveur Entity
 *
 * @property int $id
 * @property int $Services_id
 * @property int $Services_Fournisseurs_id
 * @property int $Systemes_exploitation_id
 * @property int|null $Sauvegardes_id
 * @property int|null $Sauvegardes_Services_id
 * @property int|null $Sauvegardes_Services_Fournisseurs_id
 * @property int|null $Sauvegardes_Comptes_id
 * @property string|null $nom
 * @property string|null $IP
 * @property string|resource|null $infos_tech
 * @property string|null $type
 *
 * @property \App\Model\Entity\Service $service
 * @property \App\Model\Entity\SystemesExploitation $systemes_exploitation
 * @property \App\Model\Entity\Sauvegarde $sauvegarde
 */
class Serveur extends Entity {

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
        'Sauvegardes_id' => true,
        'Sauvegardes_Services_id' => true,
        'Sauvegardes_Services_Fournisseurs_id' => true,
        'Sauvegardes_Comptes_id' => true,
        'Services_id' => true,
        'Services_Fournisseurs_id' => true,
        'Systemes_exploitation_id' => true,
        'nom' => true,
        'IP' => true,
        'infos_tech' => true,
        'type' => true,
        'service' => true,
        'systemes_exploitation' => true,
        'sauvegarde' => true,
    ];

}
