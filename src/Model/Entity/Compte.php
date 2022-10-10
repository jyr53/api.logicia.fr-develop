<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Utility\Security;
use Cake\Core\Configure\Engine\PhpConfig;

/**
 * Compte Entity
 *
 * @property int $id
 * @property string|null $login
 * @property string|null $password
 * @property string|resource|null $commentaire
 * @property string|null $type
 * @property int|null $lockself
 * @property string|null $adresse
 */
class Compte extends Entity {

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
        'login' => true,
        'password' => false,
        'commentaire' => true,
        'type' => true,
        'lockself' => true,
        'adresse' => true,
        'nom' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password',
    ];

    //
    protected function _setPassword(string $password): ?string {
        if (strlen($password) > 0) {

            //recuperer la clé "Salt" dans app.php de config
            //$salt = Configure::read('Security.salt');
            $salt = Security::getSalt();
            return Security::encrypt($password, $salt);
            //return (new DefaultPasswordHasher())->hash($password);
        }
    }

}
