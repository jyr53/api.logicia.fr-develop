<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Controller\Traits\ResponseTrait;
use Cake\Log\Log;
use CakeDC\Auth\Rbac\Rbac;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Services Controller
 *
 * @property \App\Model\Table\ServicesTable $Services
 * @method \App\Model\Entity\Service[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ServicesController extends AppController {


    //Chargement de la fonction pour le formatage de la réponse en json
    use ResponseTrait;
    /*
     * Fonction d'initialisation
     */

    public function initialize(): void {
        parent::initialize();

        //Chargement des modèles liés à la talbe Sous_Domaines
        $this->Services = $this->loadModel('Services');
        $this->Code_client = $this->loadModel('Services');

        //$this->Websites_has_comptes = $this->loadModel('Websites_has_comptes');

        //Chargement du component ApiData
        $this->loadComponent('ApiData', ['blackholeCallback' => 'blackhole']);
    }

    /**
     * Listes tous les services d'un client par rapport à son code client
     */
    public function listByCodeClient($code_client) {

        $this->autoRender = false;

        //création de la fonction getAllServices($filtre) dans le modèle
        //ajouter le filtre
        //Appel de la fonction getAllServices($filtre)
        //récupération de la liste des resultats
        $data = [];
        
        //Appel de la fonction getAllServices($filtre)
            $filtres_code_client = ['code_client' => $code_client];
            $services = $this->Services->getAllServicesCC($filtres_code_client, $code_client)->toArray();
            
       
        //parcours des résultats
        foreach ($services as $service) {
            
        //affectation dans un array data
        $data['items'][] = [
            "Services_id" => $service->id,
            "fournisseurs_id" => $service["Fournisseurs_id"],
            "nom_client" => $service->code_client,
            "renouvellement" => $service->renouvellement,
            "contrat_assistance" => $service->contrat_assistance,
            "type" => $service->type->value,
            "reference_fournisseur" => $service->reference_fournisseur
        ];
        //var_dump($code_client);
    
    }
        //return json data
        return $this->setJsonResponse($data);
        

    }

        /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index() {
        $this->paginate = [
            'contain' => ['Fournisseurs'],
        ];
        $services = $this->paginate($this->Services);

        $this->set(compact('services'));
    }

    /**
     * View method
     *
     * @param string|null $id Service id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null) {
        $service = $this->Services->get($id, [
            'contain' => ['Fournisseurs'],
        ]);

        $this->set(compact('service'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $service = $this->Services->newEmptyEntity();
        if ($this->request->is('post')) {
            $service = $this->Services->patchEntity($service, $this->request->getData()); 

            if ($this->Services->save($service)) {
                $this->Flash->success(__('The service has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The service could not be saved. Please, try again.'));
        }
        $fournisseurs = $this->Services->Fournisseurs->find('list', ['limit' => 200])->all();

        $this->set('type', $this->Services->enum('type'));
        $this->set(compact('service', 'fournisseurs'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Service id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null) {
        $service = $this->Services->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $service = $this->Services->patchEntity($service, $this->request->getData());
            if ($this->Services->save($service)) {
                $this->Flash->success(__('The service has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The service could not be saved. Please, try again.'));
        }
        $fournisseurs = $this->Services->Fournisseurs->find('list', ['limit' => 200])->all();
        $this->set('type', $this->Services->enum('type'));
        $this->set(compact('service', 'fournisseurs'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Service id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $service = $this->Services->get($id);
        if ($this->Services->delete($service)) {
            $this->Flash->success(__('The service has been deleted.'));
        } else {
            $this->Flash->error(__('The service could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}
