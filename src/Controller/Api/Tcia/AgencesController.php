<?php

declare(strict_types=1);

namespace App\Controller\Api\Tcia;

use App\Controller\AppController;
use App\Controller\Traits\ResponseTrait;
use Cake\Log\Log;
use CakeDC\Auth\Rbac\Rbac;
use GuzzleHttp\Psr7\ServerRequest;
use Cake\Auth\DefaultPasswordHasher;

/**
 * Agences Controller
 *
 * @property \App\Model\Table\AgencesTable $Agences
 * @method \App\Model\Entity\Agence[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AgencesController extends AppController {

    //Chargement de la fonction pour le formatage de la réponse en json
    use ResponseTrait;

    /*
     * Fonction d'initialisation
     */

    public function initialize(): void {
        parent::initialize();

        //Chargement des modèles liés à la talbe Domaines
        $this->Agences = $this->loadModel('Agences');

        //Chargement du component ApiData
        $this->loadComponent('ApiData', ['blackholeCallback' => 'blackhole']);
    }

    public function list() {
        //Recupere tous les Domaines depuis une fonction du model Domaine
        $agences = $this->Agences->getAllAgences()->toArray();

        return $this->setJsonResponse($agences);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index() {
        $agences = $this->paginate($this->Agences);

        $this->set(compact('agences'));
    }

    /**
     * View method
     *
     * @param string|null $id Agence id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null) {
        $agence = $this->Agences->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('agence'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $agence = $this->Agences->newEmptyEntity();
        if ($this->request->is('post')) {
            $agence = $this->Agences->patchEntity($agence, $this->request->getData());
            if ($this->Agences->save($agence)) {
                $this->Flash->success(__('The agence has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The agence could not be saved. Please, try again.'));
        }
        $this->set(compact('agence'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Agence id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null) {
        $agence = $this->Agences->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $agence = $this->Agences->patchEntity($agence, $this->request->getData());
            if ($this->Agences->save($agence)) {
                $this->Flash->success(__('The agence has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The agence could not be saved. Please, try again.'));
        }
        $this->set(compact('agence'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Agence id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $agence = $this->Agences->get($id);
        if ($this->Agences->delete($agence)) {
            $this->Flash->success(__('The agence has been deleted.'));
        } else {
            $this->Flash->error(__('The agence could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}
