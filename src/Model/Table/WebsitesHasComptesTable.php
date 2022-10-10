<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * WebsitesHasComptes Model
 *
 * @property \App\Model\Table\WebsitesTable&\Cake\ORM\Association\BelongsTo $Websites
 * @property \App\Model\Table\WebsitesTable&\Cake\ORM\Association\BelongsTo $Websites
 * @property \App\Model\Table\WebsitesTable&\Cake\ORM\Association\BelongsTo $Websites
 * @property \App\Model\Table\WebsitesTable&\Cake\ORM\Association\BelongsTo $Websites
 * @property \App\Model\Table\WebsitesTable&\Cake\ORM\Association\BelongsTo $Websites
 * @property \App\Model\Table\WebsitesTable&\Cake\ORM\Association\BelongsTo $Websites
 * @property \App\Model\Table\ComptesTable&\Cake\ORM\Association\BelongsTo $Comptes
 *
 * @method \App\Model\Entity\WebsitesHasCompte newEmptyEntity()
 * @method \App\Model\Entity\WebsitesHasCompte newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\WebsitesHasCompte[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\WebsitesHasCompte get($primaryKey, $options = [])
 * @method \App\Model\Entity\WebsitesHasCompte findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\WebsitesHasCompte patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\WebsitesHasCompte[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\WebsitesHasCompte|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\WebsitesHasCompte saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\WebsitesHasCompte[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\WebsitesHasCompte[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\WebsitesHasCompte[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\WebsitesHasCompte[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class WebsitesHasComptesTable extends Table
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

        $this->setTable('websites_has_comptes');
        $this->setDisplayField(['Websites_id', 'Websites_Services_id', 'Websites_Services_Fournisseurs_id', 'Websites_Serveurs_id', 'Websites_Serveurs_Services_id', 'Websites_Serveurs_Services_Fournisseurs_id', 'Comptes_id']);
        $this->setPrimaryKey(['Websites_id', 'Websites_Services_id', 'Websites_Services_Fournisseurs_id', 'Websites_Serveurs_id', 'Websites_Serveurs_Services_id', 'Websites_Serveurs_Services_Fournisseurs_id', 'Comptes_id']);

        $this->belongsTo('Websites', [
            'foreignKey' => 'Websites_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Websites', [
            'foreignKey' => 'Websites_Services_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Websites', [
            'foreignKey' => 'Websites_Services_Fournisseurs_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Websites', [
            'foreignKey' => 'Websites_Serveurs_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Websites', [
            'foreignKey' => 'Websites_Serveurs_Services_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Websites', [
            'foreignKey' => 'Websites_Serveurs_Services_Fournisseurs_id',
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
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->allowEmptyString('is_root');

        $validator
            ->scalar('Websites_has_Comptescol')
            ->maxLength('Websites_has_Comptescol', 45)
            ->allowEmptyString('Websites_has_Comptescol');

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
        $rules->add($rules->existsIn('Websites_id', 'Websites'), ['errorField' => 'Websites_id']);
        $rules->add($rules->existsIn('Websites_Services_id', 'Websites'), ['errorField' => 'Websites_Services_id']);
        $rules->add($rules->existsIn('Websites_Services_Fournisseurs_id', 'Websites'), ['errorField' => 'Websites_Services_Fournisseurs_id']);
        $rules->add($rules->existsIn('Websites_Serveurs_id', 'Websites'), ['errorField' => 'Websites_Serveurs_id']);
        $rules->add($rules->existsIn('Websites_Serveurs_Services_id', 'Websites'), ['errorField' => 'Websites_Serveurs_Services_id']);
        $rules->add($rules->existsIn('Websites_Serveurs_Services_Fournisseurs_id', 'Websites'), ['errorField' => 'Websites_Serveurs_Services_Fournisseurs_id']);
        $rules->add($rules->existsIn('Comptes_id', 'Comptes'), ['errorField' => 'Comptes_id']);

        return $rules;
    }

    public function getAllComptesByWebsiteID(int $website_id) {
        return $this->find()->contain(["Comptes"])->where(["Websites_id" => $website_id]);
    }
}
