<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Serveurs Model
 *
 * @property \App\Model\Table\ServicesTable&\Cake\ORM\Association\BelongsTo $Services
 * @property \App\Model\Table\ServicesTable&\Cake\ORM\Association\BelongsTo $Services
 * @property \App\Model\Table\SystemesExploitationTable&\Cake\ORM\Association\BelongsTo $SystemesExploitation
 * @property \App\Model\Table\SauvegardesTable&\Cake\ORM\Association\BelongsTo $Sauvegardes
 * @property \App\Model\Table\SauvegardesTable&\Cake\ORM\Association\BelongsTo $Sauvegardes
 * @property \App\Model\Table\SauvegardesTable&\Cake\ORM\Association\BelongsTo $Sauvegardes
 * @property \App\Model\Table\SauvegardesTable&\Cake\ORM\Association\BelongsTo $Sauvegardes
 *
 * @method \App\Model\Entity\Serveur newEmptyEntity()
 * @method \App\Model\Entity\Serveur newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Serveur[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Serveur get($primaryKey, $options = [])
 * @method \App\Model\Entity\Serveur findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Serveur patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Serveur[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Serveur|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Serveur saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Serveur[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Serveur[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Serveur[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Serveur[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ServeursTable extends Table {

    const TYPE_DEDIE = 'DÉDIÉ';
    const TYPE_VPS = 'VPS';

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

        $this->setTable('serveurs');
        $this->setDisplayField(['id', 'Services_id', 'Services_Fournisseurs_id', 'Systemes_exploitation_id']);
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
        $this->belongsTo('SystemesExploitation', [
            'foreignKey' => 'Systemes_exploitation_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Sauvegardes', [
            'foreignKey' => 'Sauvegardes_id',
            'bindingKey' => 'id',
        ]);
        $this->belongsTo('Sauvegardes', [
            'foreignKey' => 'Sauvegardes_Services_id',
            'bindingKey' => 'Services_id',
        ]);
        $this->belongsTo('Sauvegardes', [
            'foreignKey' => 'Sauvegardes_Services_Fournisseurs_id',
            'bindingKey' => 'Fournisseurs_id',
        ]);
        $this->belongsTo('Sauvegardes', [
            'foreignKey' => 'Sauvegardes_Comptes_id',
            'bindingKey' => 'Comptes_id',
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
                ->allowEmptyString('nom');

        $validator
                ->scalar('IP')
                ->maxLength('IP', 45)
                ->allowEmptyString('IP');

        $validator
                ->allowEmptyString('infos_tech');

        $validator
                ->scalar('type')
                ->allowEmptyString('type');

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
                        ['nom', 'Services_id'],
        ));

        //$rules->add($rules->existsIn('Services_id', 'Services'), ['errorField' => 'Services_id']);
        $rules->add($rules->existsIn('Services_Fournisseurs_id', 'Services'), ['errorField' => 'Services_Fournisseurs_id']);
        $rules->add($rules->existsIn('Systemes_exploitation_id', 'SystemesExploitation'), ['errorField' => 'Systemes_exploitation_id']);
        $rules->add($rules->existsIn('Sauvegardes_id', 'Sauvegardes'), ['errorField' => 'Sauvegardes_id']);
        $rules->add($rules->existsIn('Sauvegardes_Services_id', 'Sauvegardes'), ['errorField' => 'Sauvegardes_Services_id']);
        $rules->add($rules->existsIn('Sauvegardes_Services_Fournisseurs_id', 'Sauvegardes'), ['errorField' => 'Sauvegardes_Services_Fournisseurs_id']);
        //$rules->add($rules->existsIn('Sauvegardes_Comptes_id', 'Sauvegardes'), ['errorField' => 'Sauvegardes_Comptes_id']);

        return $rules;
    }

    public function getAllServeurs() {
        return $this->find();
    }

    public function getAllServeur(array $filtres) {
        return $this->find()->where($filtres);
    }
    
    public function getServeurAndNom(array $filtres) {
        return $this->find()->contain(["Nom"])->where($filtres);
    }

}
