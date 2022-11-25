<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Log\Log;

/**
 * Transports Model
 *
 * @property \App\Model\Table\AgencesTable&\Cake\ORM\Association\BelongsTo $Agences
 * @property \App\Model\Table\AgencesTable&\Cake\ORM\Association\BelongsTo $Agences
 * @property \App\Model\Table\InterventionsTable&\Cake\ORM\Association\BelongsTo $Interventions
 *
 * @method \App\Model\Entity\Transport newEmptyEntity()
 * @method \App\Model\Entity\Transport newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Transport[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Transport get($primaryKey, $options = [])
 * @method \App\Model\Entity\Transport findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Transport patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Transport[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Transport|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Transport saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Transport[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Transport[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Transport[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Transport[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class TransportsTable extends Table
{

        /**
         * Initialize method
         *
         * @param array $config The configuration for the Table.
         * @return void
         */
        public function initialize(array $config): void
        {
                parent::initialize($config);

                $this->setTable('transports');
                $this->setDisplayField('id');
                $this->setPrimaryKey('id');

                $this->belongsTo('Agences1', [
                        'foreignKey' => 'agence_depart_aller_id',
                        'joinType' => 'LEFT',
                        'className' => 'Agences'
                ]);
                $this->belongsTo('Agences2', [
                        'foreignKey' => 'agence_arrivee_aller_id',
                        'joinType' => 'LEFT',
                        'className' => 'Agences'
                ]);
                $this->belongsTo('Agences3', [
                        'foreignKey' => 'agence_depart_retour_id',
                        'joinType' => 'LEFT',
                        'className' => 'Agences'
                ]);
                $this->belongsTo('Agences4', [
                        'foreignKey' => 'agence_arrivee_retour_id',
                        'joinType' => 'LEFT',
                        'className' => 'Agences'
                ]);

        }

        /**
         * Default validation rules.
         *
         * @param \Cake\Validation\Validator $validator Validator instance.
         * @return \Cake\Validation\Validator
         */
        public function validationDefault(Validator $validator): Validator
        {
                $validator
                        ->integer('id')
                        ->allowEmptyString('id', null, 'create');

                $validator
                        ->scalar('nom_contact')
                        ->maxLength('nom_contact', 255)
                        ->requirePresence('nom_contact', 'create')
                        ->notEmptyString('nom_contact');

                $validator
                        ->scalar('num_client')
                        ->maxLength('num_client', 20)
                        ->requirePresence('num_client', 'create')
                        ->notEmptyString('num_client');

                $validator
                        ->scalar('tel_contact')
                        ->maxLength('tel_contact', 20)
                        ->requirePresence('tel_contact', 'create')
                        ->notEmptyString('tel_contact');

                $validator
                        ->scalar('email_contact')
                        ->requirePresence('email_contact', 'create')
                        ->notEmptyString('email_contact');

                $validator
                        ->scalar('contenu')
                        ->requirePresence('contenu', 'create')
                        ->notEmptyString('contenu');

                $validator
                        ->scalar('motif')
                        ->requirePresence('motif', 'create')
                        ->notEmptyString('motif');

                $validator
                        ->integer('nb_colis')
                        ->requirePresence('nb_colis', 'create')
                        ->notEmptyString('nb_colis');

                $validator
                        ->date('date_depart_aller')
                        ->allowEmptyDate('date_depart_aller');
                $validator
                        ->date('date_arrivee_aller')
                        ->allowEmptyDate('date_arrivee_aller');
                $validator
                        ->date('date_depart_retour')
                        ->allowEmptyDate('date_depart_retour');
                $validator
                        ->date('date_arrivee_retour')
                        ->allowEmptyDate('date_arrivee_retour');
                $validator
                        ->scalar('expediteur')
                        ->requirePresence('expediteur', 'create')
                        ->notEmptyString('expediteur');

                return $validator;
        }

        /**
         * Returns a rules checker object that will be used for validating
         * application integrity.
         *
         * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
         * @return \Cake\ORM\RulesChecker
         */
        public function buildRules(RulesChecker $rules): RulesChecker
        {
                $rules->add($rules->existsIn('agence_depart_aller_id', 'Agences1'), ['errorField' => 'agence_depart_aller_id']);
                $rules->add($rules->existsIn('agence_arrivee_aller_id', 'Agences2'), ['errorField' => 'agence_arrivee_aller_id']);
                $rules->add($rules->existsIn('agence_depart_retour_id', 'Agences3'), ['errorField' => 'agence_depart_retour_id']);
                $rules->add($rules->existsIn('agence_arrivee_retour_id', 'Agences4'), ['errorField' => 'agence_arrivee_retour_id']);

                return $rules;
        }

        public function getAllTransports()
        {
                return $this->find()->contain(["Agences1", "Agences2", "Agences3", "Agences4"]);
        }
        /**
         * Returns the database connection name to use by default.
         *
         * @return string
         */

        /*
    public static function defaultConnectionName(): string {
        return 'tcia';
    }
	*/
}
