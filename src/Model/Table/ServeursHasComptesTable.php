<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ServeursHasComptes Model
 *
 * @property \App\Model\Table\ServeursTable&\Cake\ORM\Association\BelongsTo $Serveurs
 * @property \App\Model\Table\ServeursTable&\Cake\ORM\Association\BelongsTo $Serveurs
 * @property \App\Model\Table\ServeursTable&\Cake\ORM\Association\BelongsTo $Serveurs
 * @property \App\Model\Table\ComptesTable&\Cake\ORM\Association\BelongsTo $Comptes
 *
 * @method \App\Model\Entity\ServeursHasCompte newEmptyEntity()
 * @method \App\Model\Entity\ServeursHasCompte newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\ServeursHasCompte[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ServeursHasCompte get($primaryKey, $options = [])
 * @method \App\Model\Entity\ServeursHasCompte findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\ServeursHasCompte patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ServeursHasCompte[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ServeursHasCompte|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ServeursHasCompte saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ServeursHasCompte[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ServeursHasCompte[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\ServeursHasCompte[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\ServeursHasCompte[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ServeursHasComptesTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('serveurs_has_comptes');
        $this->setDisplayField(['Serveurs_id', 'Serveurs_Services_id', 'Serveurs_Services_Fournisseurs_id', 'Comptes_id']);
        $this->setPrimaryKey(['Serveurs_id']);

        $this->belongsTo('Serveurs', [
            'foreignKey' => 'Serveurs_id',
            'bindingKey' => 'id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Serveurs', [
            'foreignKey' => 'Serveurs_Services_id',
            'bindingKey' => 'Services_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Serveurs', [
            'foreignKey' => 'Serveurs_Services_Fournisseurs_id',
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
                ->allowEmptyString('is_root');

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
        $rules->add($rules->existsIn('Serveurs_id', 'Serveurs'), ['errorField' => 'Serveurs_id']);
        $rules->add($rules->existsIn('Serveurs_Services_id', 'Serveurs'), ['errorField' => 'Serveurs_Services_id']);
        $rules->add($rules->existsIn('Serveurs_Services_Fournisseurs_id', 'Serveurs'), ['errorField' => 'Serveurs_Services_Fournisseurs_id']);
        $rules->add($rules->existsIn('Comptes_id', 'Comptes'), ['errorField' => 'Comptes_id']);

        return $rules;
    }

    public function getAllComptesByServerID(int $serveur_id) {
        return $this->find()->contain(["Comptes"])->where(["Serveurs_id" => $serveur_id]);
    }

}
