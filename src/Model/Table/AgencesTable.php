<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Agences Model
 *
 * @method \App\Model\Entity\Agence newEmptyEntity()
 * @method \App\Model\Entity\Agence newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Agence[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Agence get($primaryKey, $options = [])
 * @method \App\Model\Entity\Agence findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Agence patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Agence[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Agence|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Agence saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Agence[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Agence[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Agence[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Agence[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class AgencesTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('agences');
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
                ->allowEmptyString('id', null, 'create');

        $validator
                ->scalar('nom')
                ->maxLength('nom', 255)
                ->requirePresence('nom', 'create')
                ->notEmptyString('nom');

        $validator
                ->integer('departement')
                ->maxLength('departement', 2)
                ->requirePresence('departement', 'create')
                ->notEmptyString('departement');

        $validator
                ->scalar('adresse1')
                ->allowEmptyString('adresse1');

        $validator
                ->scalar('adresse2')
                ->allowEmptyString('adresse2');
        $validator
                ->integer('cp')
                ->allowEmptyString('cp');
        $validator
                ->scalar('ville')
                ->allowEmptyString('departement');
        $validator
                ->scalar('transporteur')
                ->requirePresence('departement', 'create')
                ->notEmptyString('departement');
        $validator
                ->scalar('username')
                ->notEmptyString('username');

        $validator
                ->scalar('password')
                ->notEmptyString('password');

        return $validator;
    }

    public function getAllAgences() {
        return $this->find();
    }

    public function getByUsername($username) {
        return $this->find()->where(['username' => $username]);
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    /*public static function defaultConnectionName(): string {
        return 'tcia';
    }
*/
}
