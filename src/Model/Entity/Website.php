<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Website Entity
 *
 * @property int $id
 * @property int $Services_id
 * @property int $Services_Fournisseurs_id
 * @property int $Serveurs_id
 * @property int $Serveurs_Services_id
 * @property int $Serveurs_Services_Fournisseurs_id
 * @property int|null $Sauvegardes_id
 * @property int|null $Sauvegardes_Services_id
 * @property int|null $Sauvegardes_Services_Fournisseurs_id
 * @property int|null $Sauvegardes_Comptes_id
 * @property string|null $IP
 * @property string|resource|null $commentaire
 * @property string|null $licence_theme
 * @property string|null $plateteforme
 * @property string|null $version
 * @property string|resource|null $plateforme_commentaire
 *
 * @property \App\Model\Entity\Service $service
 * @property \App\Model\Entity\Serveur $serveur
 * @property \App\Model\Entity\Sauvegarde $sauvegarde
 */
class Website extends Entity {

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
        'Serveurs_id' => true,
        'Serveurs_Services_id' => true,
        'Serveurs_Services_Fournisseurs_id' => true,
        'IP' => true,
        'commentaire' => true,
        'licence_theme' => true,
        'plateteforme' => true,
        'version' => true,
        'plateforme_commentaire' => true,
        'service' => true,
        'serveur' => true,
        'sauvegarde' => true,
    ];

}
