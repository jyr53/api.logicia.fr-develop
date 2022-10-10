<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Domaines Model
 *
 * @property \App\Model\Table\ServicesTable&\Cake\ORM\Association\BelongsTo $Services
 * @property \App\Model\Table\ServicesTable&\Cake\ORM\Association\BelongsTo $Services
 *
 * @method \App\Model\Entity\Domaine newEmptyEntity()
 * @method \App\Model\Entity\Domaine newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Domaine[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Domaine get($primaryKey, $options = [])
 * @method \App\Model\Entity\Domaine findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Domaine patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Domaine[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Domaine|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Domaine saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Domaine[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Domaine[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Domaine[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Domaine[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class DomainesTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('domaines');
        $this->setDisplayField(['id', 'Services_id', 'Services_Fournisseurs_id']);
        $this->setPrimaryKey(['id']);

        $this->belongsTo('Services', [
            'foreignKey' => 'Services_id',
            'bindingKey' => 'id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Services', [
            'foreignKey' => 'Services_Fournisseurs_id',
            'bindingKey' => 'Fournisseurs_id',
            'joinType' => 'INNER'
        ]);
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
                ->scalar('nom')
                ->maxLength('nom', 255)
                ->requirePresence('nom', 'create')
                ->notEmptyString('nom')
                ->add('nom', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

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

        // Une liste de champs
        $rules->add($rules->isUnique(
                        ['Services_id', 'Services_Fournisseurs_id', 'nom'],
        ));
        //$rules->add($rules->existsIn('Services_id', 'Services'), ['errorField' => 'Services_id']);
        $rules->add($rules->existsIn('Services_Fournisseurs_id', 'Services'), ['errorField' => 'Services_Fournisseurs_id']);

        return $rules;
    }



    public function getAllDomaines() {
        return $this->find();
    }


} 
