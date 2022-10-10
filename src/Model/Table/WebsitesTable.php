<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Websites Model
 *
 * @property \App\Model\Table\ServicesTable&\Cake\ORM\Association\BelongsTo $Services
 * @property \App\Model\Table\ServicesTable&\Cake\ORM\Association\BelongsTo $Services
 * @property \App\Model\Table\ServeursTable&\Cake\ORM\Association\BelongsTo $Serveurs
 * @property \App\Model\Table\ServeursTable&\Cake\ORM\Association\BelongsTo $Serveurs
 * @property \App\Model\Table\ServeursTable&\Cake\ORM\Association\BelongsTo $Serveurs
 * @property \App\Model\Table\SauvegardesTable&\Cake\ORM\Association\BelongsTo $Sauvegardes
 * @property \App\Model\Table\SauvegardesTable&\Cake\ORM\Association\BelongsTo $Sauvegardes
 * @property \App\Model\Table\SauvegardesTable&\Cake\ORM\Association\BelongsTo $Sauvegardes
 * @property \App\Model\Table\SauvegardesTable&\Cake\ORM\Association\BelongsTo $Sauvegardes
 *
 * @method \App\Model\Entity\Website newEmptyEntity()
 * @method \App\Model\Entity\Website newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Website[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Website get($primaryKey, $options = [])
 * @method \App\Model\Entity\Website findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Website patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Website[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Website|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Website saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Website[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Website[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Website[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Website[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class WebsitesTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('websites');
        $this->setDisplayField(['id', 'Services_id', 'Services_Fournisseurs_id', 'Serveurs_id', 'Serveurs_Services_id', 'Serveurs_Services_Fournisseurs_id']);
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
            'bindingKey' => 'Services_Fournisseurs_id',
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
            'bindingKey' => 'Services_Fournisseurs_id',
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
                ->scalar('IP')
                ->maxLength('IP', 45)
                ->allowEmptyString('IP');

        $validator
                ->allowEmptyString('commentaire');

        $validator
                ->scalar('licence_theme')
                ->maxLength('licence_theme', 45)
                ->allowEmptyString('licence_theme');

        $validator
                ->scalar('plateteforme')
                ->maxLength('plateteforme', 45)
                ->allowEmptyString('plateteforme');

        $validator
                ->scalar('version')
                ->maxLength('version', 10)
                ->allowEmptyString('version');

        $validator
                ->allowEmptyString('plateforme_commentaire');

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
       // $rules->add($rules->existsIn('Serveurs_id', 'Serveurs'), ['errorField' => 'Serveurs_id']);
        //$rules->add($rules->existsIn('Serveurs_Services_id', 'Serveurs'), ['errorField' => 'Serveurs_Services_id']);
        $rules->add($rules->existsIn('Serveurs_Services_Fournisseurs_id', 'Serveurs'), ['errorField' => 'Serveurs_Services_Fournisseurs_id']);
        $rules->add($rules->existsIn('Sauvegardes_id', 'Sauvegardes'), ['errorField' => 'Sauvegardes_id']);
        $rules->add($rules->existsIn('Sauvegardes_Services_id', 'Sauvegardes'), ['errorField' => 'Sauvegardes_Services_id']);
        $rules->add($rules->existsIn('Sauvegardes_Services_Fournisseurs_id', 'Sauvegardes'), ['errorField' => 'Sauvegardes_Services_Fournisseurs_id']);
        $rules->add($rules->existsIn('Sauvegardes_Comptes_id', 'Sauvegardes'), ['errorField' => 'Sauvegardes_Comptes_id']);

        return $rules;
    }
    
    public function getAllWebsites() {
        return $this->find();
    }

    public function getWebsite(array $filtres) {
        return $this->find()->where($filtres);
    }

}
