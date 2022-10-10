<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Sauvegardes Model
 *
 * @property \App\Model\Table\ServicesTable&\Cake\ORM\Association\BelongsTo $Services
 * @property \App\Model\Table\ServicesTable&\Cake\ORM\Association\BelongsTo $Services
 * @property \App\Model\Table\ComptesTable&\Cake\ORM\Association\BelongsTo $Comptes
 *
 * @method \App\Model\Entity\Sauvegarde newEmptyEntity()d
 * @method \App\Model\Entity\Sauvegarde newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Sauvegarde[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Sauvegarde get($primaryKey, $options = [])
 * @method \App\Model\Entity\Sauvegarde findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Sauvegarde patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Sauvegarde[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Sauvegarde|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Sauvegarde saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Sauvegarde[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Sauvegarde[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Sauvegarde[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Sauvegarde[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SauvegardesTable extends Table {

    const FREQUENCE_QUOTIDIENNE = 'QUOTIDIENNE';
    const FREQUENCE_HEBDOMADAIRE = 'HEBDOMADAIRE';
    const FREQUENCE_MENSUELLE = 'MENSUELLE';

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void {
        parent::initialize($config);

        $this->addBehavior('CakeDC/Enum.Enum', ['lists' => [
                'frequence' => [
                    'strategy' => 'const',
                    'prefix' => 'FREQUENCE'
                ]
        ]]);

        $this->setTable('sauvegardes');
        $this->setDisplayField(['id', 'Services_id', 'Services_Fournisseurs_id', 'Comptes_id']);
        $this->setPrimaryKey(['id']);

        $this->belongsTo('Services', [
            'foreignKey' => 'Services_id',
            'bindingKey' => 'id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Services', [
            'foreignKey' => 'Services_Fournisseurs_id',
            'bindingKey' => 'Fournisseurs_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Comptes', [
            'foreignKey' => 'Comptes_id',
            'joinType' => 'INNER',
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
                ->dateTime('derniere')
                ->allowEmptyDateTime('derniere');

        $validator
                ->allowEmptyString('commentaire');

        $validator
                ->scalar('frequence')
                ->allowEmptyString('frequence');

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
        //$rules->add($rules->existsIn('Services_id', 'Services'), ['errorField' => 'Services_id']);
        $rules->add($rules->existsIn('Services_Fournisseurs_id', 'Services'), ['errorField' => 'Services_Fournisseurs_id']);
        $rules->add($rules->existsIn('Comptes_id', 'Comptes'), ['errorField' => 'Comptes_id']);

        return $rules;
    }

    public function getAllSauvegarde() {
        return $this->find();
    }

}
