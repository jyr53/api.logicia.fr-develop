<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Controller\Traits\ResponseTrait;
use Cake\Log\Log;
use CakeDC\Auth\Rbac\Rbac;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Sous_Domaines Controller
 *
 * @property \App\Model\Table\ComptesTable $Sous_Domaines
 * @method \App\Model\Entity\Sous_Domaines[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ComptesController extends AppController {

    //Chargement de la fonction pour le formatage de la réponse en json
    use ResponseTrait;

    /*
     * Fonction d'initialisation
     */

    public function initialize(): void {
        parent::initialize();

        //Chargement des modèles liés à la talbe Sous_Domaines
        $this->Comptes = $this->loadModel('Comptes');
        $this->Lockselfs = $this->loadModel('Comptes');

        //$this->Websites_has_comptes = $this->loadModel('Websites_has_comptes');
        //Chargement du component ApiData
        $this->loadComponent('ApiData', ['blackholeCallback' => 'blackhole']);
    }

    /*
     * Fonction List : Listes tous les Sous_Domaines et leurs dépendances ou enfants.
     * return $data
     */

    public function listLockself() {

        $this->autoRender = false; // pour ne pas rendre une vue (template) cake
        //Recupere tous les Sous_Domaines depuis une fonction du model Sous_omaines
        $comptes = $this->Comptes->getAllLockself()->toArray();

        //Tableau de données retourné vers vuejs - tableau final
        $data = [];

        //Parcours tous mes Sous_Domaines
        foreach ($comptes as $compte) {


            $data['items'][] = [
                "comptes_id" => $compte->id,
                "login" => $compte->login,
                "commentaire" => $compte->commentaire,
                "type" => $compte->type->value,
                "adresse" => $compte->adresse,
                "nom" => $compte->nom
            ];
        }

        $data["headers"] = [
            [
                "visible" => false,
                "sortable" => false,
                "searchable" => false,
                "text" => "Comptes_id",
                "value" => "comptes_id",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Login",
                "value" => "login",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Commentaire",
                "value" => "commentaire",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Type",
                "value" => "type",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Adresse",
                "value" => "adresse",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Nom",
                "value" => "nom",
                "divider" => true
            ],
        ];

        $data["sort_by"] = "serveur_id";
        $data["sort_desc"] = false;
        $data["items_per_page"] = 50;

        //var_dump($data);
        return $this->setJsonResponse($data);
    }

    /**
     * View method
     *
     * @param string|null $id Compte id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null) {
        $compte = $this->Comptes->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('compte'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $compte = $this->Comptes->newEmptyEntity();
        if ($this->request->is('post')) {
            $compte = $this->Comptes->patchEntity($compte, $this->request->getData());
            if ($this->Comptes->save($compte)) {
                $this->Flash->success(__('The compte has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The compte could not be saved. Please, try again.'));
        }
        $this->set('type', $this->Comptes->enum('type'));
        $this->set(compact('compte'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Compte id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null) {
        $compte = $this->Comptes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $compte = $this->Comptes->patchEntity($compte, $this->request->getData());
            if ($this->Comptes->save($compte)) {
                $this->Flash->success(__('The compte has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The compte could not be saved. Please, try again.'));
        }
        $this->set('type', $this->Comptes->enum('type'));
        $this->set(compact('compte'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Compte id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $compte = $this->Comptes->get($id);
        if ($this->Comptes->delete($compte)) {
            $this->Flash->success(__('The compte has been deleted.'));
        } else {
            $this->Flash->error(__('The compte could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function getDPassword($compte_id) {

        $this->autoRender = false; // pour ne pas rendre une vue (template) cake
        $compte = $this->Comptes->get($compte_id);
        $dpassword = $this->Comptes->getDPassword(stream_get_contents($compte->password));

        return $this->setJsonResponse($dpassword);
    }

}
