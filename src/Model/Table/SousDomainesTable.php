<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SousDomaines Model
 *
 * @property \App\Model\Table\DomainesTable&\Cake\ORM\Association\BelongsTo $Domaines
 * @property \App\Model\Table\DomainesTable&\Cake\ORM\Association\BelongsTo $Domaines
 * @property \App\Model\Table\DomainesTable&\Cake\ORM\Association\BelongsTo $Domaines
 * @property \App\Model\Table\WebsitesTable&\Cake\ORM\Association\BelongsTo $Websites
 * @property \App\Model\Table\WebsitesTable&\Cake\ORM\Association\BelongsTo $Websites
 * @property \App\Model\Table\WebsitesTable&\Cake\ORM\Association\BelongsTo $Websites
 *
 * @method \App\Model\Entity\SousDomaine newEmptyEntity()
 * @method \App\Model\Entity\SousDomaine newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\SousDomaine[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SousDomaine get($primaryKey, $options = [])
 * @method \App\Model\Entity\SousDomaine findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\SousDomaine patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SousDomaine[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SousDomaine|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SousDomaine saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SousDomaine[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SousDomaine[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\SousDomaine[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SousDomaine[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SousDomainesTable extends Table {

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('sous_domaines');
        $this->setDisplayField(['id', 'Domaines_id', 'Domaines_Services_id', 'Domaines_Services_Fournisseurs_id', 'Websites_id', 'Websites_Services_id', 'Websites_Services_Fournisseurs_id']);
        $this->setPrimaryKey(['id']);

        $this->belongsTo('Domaines', [
            'foreignKey' => 'Domaines_id',
            'bindingKey' => 'id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Domaines', [
            'foreignKey' => 'Domaines_Services_id',
            'bindingKey' => 'Services_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Domaines', [
            'foreignKey' => 'Domaines_Services_Fournisseurs_id',
            'bindingKey' => 'Services_Fournisseurs_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Websites', [
            'foreignKey' => 'Websites_id',
            'bindingKey' => 'id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Websites', [
            'foreignKey' => 'Websites_Services_id',
            'bindingKey' => 'Services_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Websites', [
            'foreignKey' => 'Websites_Services_Fournisseurs_id',
            'bindingKey' => 'Services_Fournisseurs_id',
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
                ->scalar('nom')
                ->maxLength('nom', 255)
                ->allowEmptyString('nom');

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
//        $rules->add($rules->isUnique(
//                        ['nom', 'Domaines_id'],
//        ));

        //$rules->add($rules->existsIn('Domaines_id', 'Domaines'), ['errorField' => 'Domaines_id']);
        //$rules->add($rules->existsIn('Domaines_Services_id', 'Domaines'), ['errorField' => 'Domaines_Services_id']);
        $rules->add($rules->existsIn('Domaines_Services_Fournisseurs_id', 'Domaines'), ['errorField' => 'Domaines_Services_Fournisseurs_id']);
        $rules->add($rules->existsIn('Websites_id', 'Websites'), ['errorField' => 'Websites_id']);
        //$rules->add($rules->existsIn('Websites_Services_id', 'Websites'), ['errorField' => 'Websites_Services_id']);
        $rules->add($rules->existsIn('Websites_Services_Fournisseurs_id', 'Websites'), ['errorField' => 'Websites_Services_Fournisseurs_id']);

        return $rules;
    }
    public function getAllSousDomainesByWebsiteID(int $website_id) {
        return $this->find()->where(["Websites_id" => $website_id]);
    }
    
    public function getAllSousDomainesByDomaineID(int $domaine_id) {
        return $this->find()->where(["Domaines_id" => $domaine_id]);
    }

    public function getAllSousDomaines() {
        return $this->find();
    }

}
