<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Services Model
 *
 * @property \App\Model\Table\FournisseursTable&\Cake\ORM\Association\BelongsTo $Fournisseurs
 *
 * @method \App\Model\Entity\Service newEmptyEntity()
 * @method \App\Model\Entity\Service newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Service[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Service get($primaryKey, $options = [])
 * @method \App\Model\Entity\Service findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Service patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Service[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Service|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Service saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Service[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Service[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Service[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Service[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class ServicesTable extends Table {

    const TYPE_DOMAINE = 'DOMAINE';
    const TYPE_WEBSITE = 'WEBSITE';
    const TYPE_SERVEUR = 'SERVEUR';
    const TYPE_SAUVEGARDE = 'SAUVEGARDE';
    const TYPE_MXPLAN = 'MXPLAN';
    const TYPE_VEEAM = 'VEEAM';
    const TYPE_OFFICE365 = 'OFFICE365';
    const TYPE_PLESK = 'PLESK';
    const TYPE_GOOGLE_WORKSPACE = 'GOOGLE WORSPACE';

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

        $this->setTable('services');
        $this->setDisplayField(['id', 'Fournisseurs_id']);
        $this->setPrimaryKey(['id']);

        $this->belongsTo('Fournisseurs', [
            'foreignKey' => 'Fournisseurs_id',
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
                ->scalar('code_client')
                ->maxLength('code_client', 45)
                ->allowEmptyString('code_client');

        $validator
                ->scalar('nom_client')
                ->maxLength('nom_client', 45)
                ->allowEmptyString('nom_client');

        $validator
                ->date('renouvellement')
                ->allowEmptyDate('renouvellement');

        $validator
                ->allowEmptyString('contrat_assistance');

        $validator
                ->scalar('type')
                ->allowEmptyString('type');

        $validator
                ->scalar('reference_fournisseur')
                ->maxLength('reference_fournisseur', 255)
                ->allowEmptyString('reference_fournisseur');

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
        $rules->add($rules->existsIn('Fournisseurs_id', 'Fournisseurs'), ['errorField' => 'Fournisseurs_id']);

        return $rules;
    }

    /*
     * Récupération du service et du fournisseur de l'entité en cours
     * @param array de filtres pour le where
     * @return \Cake\ORM\
     */

    public function getServiceAndFournisseur(array $filtres) {
        return $this->find()->contain(["Fournisseurs"])->where($filtres);
    }

      /*
     * Récupération de la liste des services
     * @param array de filtres pour le where - pas obligatoire
     * @return \Cake\ORM\
     */


    public function getAllServices() {
        return $this->find();
    }

    public function getAllServicesCC(array $filtres/*, string $code_client*/) {
    return $this->find()->where($filtres, /*[$code_client => 'code_client']*/);
    }

    

}
