<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Security;

/**
 * Comptes Model
 *
 * @method \App\Model\Entity\Compte newEmptyEntity()
 * @method \App\Model\Entity\Compte newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Compte[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Compte get($primaryKey, $options = [])
 * @method \App\Model\Entity\Compte findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Compte patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Compte[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Compte|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Compte saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Compte[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Compte[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Compte[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Compte[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ComptesTable extends Table {

    const TYPE_FTP = 'FTP';
    const TYPE_SSH = 'SSH';
    const TYPE_BDD = 'BDD';
    const TYPE_ADMIN = 'ADMIN';
    const TYPE_EMAIL = 'EMAIL';
    const TYPE_PLESK = 'PLESK';
    const TYPE_AUTRES = 'AUTRES';

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void {
        parent::initialize($config);

        $this->addBehavior('CakeDC/Enum.Enum', ['lists' => [
                'type' => [
                    'strategy' => 'const',
                    'prefix' => 'TYPE'
                ]
        ]]);

        $this->setTable('comptes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator {
        $validator
                ->integer('id')
                ->allowEmptyString('id', 'create');

        $validator
                ->scalar('login')
                ->maxLength('login', 45)
                ->notEmptyString('login');

        $validator
                ->scalar('password')
                ->maxLength('password', 200)
                ->notEmptyString('password');

        $validator
                ->allowEmptyString('commentaire');

        $validator
                ->scalar('type')
                ->notEmptyString('type');

        $validator
                ->allowEmptyString('lockself');

        $validator
                ->scalar('adresse')
                ->maxLength('adresse', 255)
                ->allowEmptyString('adresse');

        $validator
                ->scalar('nom')
                ->maxLength('nom', 255)
                ->notEmptyString('nom');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker {
        $rules->add($rules->isUnique(['login']), ['errorField' => 'login']);

        return $rules;
    }

    public function getAllComptes() {
        return $this->find();
    }

    public function getAllLockself() {
        //Seelct * from comptes where lockself =1
        return $this->find()->where(["lockself" => 1]);
    }

    public function getDPassword($password) {

        $salt = Security::getSalt();
        return Security::decrypt($password, $salt);
    }

    /* public function getAllLockselfsByCompteID(array $filtres) {
      return $this->find()->where($filtres == 1);
      } */
}
