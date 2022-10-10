<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Service Entity
 *
 * @property int $id
 * @property int $Fournisseurs_id
 * @property string|null $code_client
 * @property string|null $nom_client
 * @property \Cake\I18n\FrozenDate|null $renouvellement
 * @property int|null $contrat_assistance
 * @property string|null $type
 * @property string|null $reference_fournisseur
 *
 * @property \App\Model\Entity\Fournisseur $fournisseur
 */
class Service extends Entity {

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
        'code_client' => true,
        'nom_client' => true,
        'renouvellement' => true,
        'contrat_assistance' => true, 
        'type' => true,
        'reference_fournisseur' => true,
        'fournisseur' => true,
        'Fournisseurs_id' => true,
    ];

}
