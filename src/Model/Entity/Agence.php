<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Agence Entity
 *
 * @property int $id
 * @property string $nom
 */
class Agence extends Entity {

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
        'departement' => true,
        'adresse1' => true,
        'adresse2' => true,
        'ville' => true,
        'cp' => true,
        'transporteur' => true,
        'username' => true,
        'password' => false
    ];

}
