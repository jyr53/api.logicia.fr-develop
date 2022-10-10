<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Controller\Traits\ResponseTrait;
use Cake\Log\Log;
use CakeDC\Auth\Rbac\Rbac;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Domaines Controller
 *
 * @property \App\Model\Table\DomainesTable $Domaines
 * @method \App\Model\Entity\Server[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DomainesController extends AppController {

    //Chargement de la fonction pour le formatage de la réponse en json
    use ResponseTrait;

    /*
     * Fonction d'initialisation
     */

    public function initialize(): void {
        parent::initialize();

        //Chargement des modèles liés à la talbe Domaines
        $this->Services = $this->loadModel('Services');
        $this->SousDomaines = $this->loadModel('SousDomaines');

        //Chargement du component ApiData
        $this->loadComponent('ApiData', ['blackholeCallback' => 'blackhole']);
    }

    /*
     * Fonction List : Listes tous les Domaines et leurs dépendances ou enfants.
     * return $data
     */

    public function list() {

        $this->autoRender = false; // pour ne pas rendre une vue (template) cake
        //
        //
        //Récupération de toutes les actions (fonctions) possibles pour les Domaines
        $all_actions = $this->ApiData->getActions($this->request->getAttribute('params')['controller'], $this->request->getAttribute('params')['prefix']);

        // Obtenir l'identity à partir de la requête
        $user = $this->request->getAttribute('identity');

        $rbac = new Rbac();
        $auth_actions = [];
        foreach ($all_actions['Domaines'] as $action) {
            $request = new ServerRequest('GET', '');
            $request->withAttribute('params', [
                'prefix' => 'Api',
                'plugin' => null,
                'extension' => null,
                'controller' => 'Domaines',
                'action' => $action,
                'role' => 'superuser',
            ]);
            if ($rbac->checkPermissions($user, $request))
                array_push($auth_actions, $action);
        }

        //Recupere tous les Domaines depuis une fonction du model Domaine
        $domaines = $this->Domaines->getAllDomaines()->toArray();

        //Tableau de données retourné vers vuejs - tableau final
        $data = [];

        //Parcours tous mes Domaines
        foreach ($domaines as $domaine) {

            $filtres_services = ['Services.id' => $domaine->Services_id];

            //Récupération du service du domaine en cours dans le foreach
            $service = $this->Services->getServiceAndFournisseur($filtres_services)->toArray();

            $sousDomaines = $this->SousDomaines->getAllSousDomainesByDomaineID($domaine->id)->toArray();

            $sousDomainesDomaines = [];

            foreach ($sousDomaines as $sousDomaine):
                $sousDomainesDomaines[] = [
                    "nom" => $sousDomaine->nom,
                    "id" => $sousDomaine->id,
                ];
            endforeach;

            //Log::debug(print_r($service, true));

            $data['items'][] = [
                "domaine_id" => $domaine->id,
                "services_id" => $domaine->Services_id,
                "fournisseurs_id" => $service[0]["Fournisseurs_id"],
                "nom" => $domaine->nom,
                "code_client" => $service[0]["code_client"],
                "nom_client" => $service[0]["nom_client"],
                "renouvellement" => $service[0]["renouvellement"],
                "assistance" => ($service[0]["contrat_assistance"] == 1) ? "OUI" : "NON",
                "reference_fournisseur" => $service[0]["reference_fournisseur"],
                "fournisseur" => $service[0]["fournisseur"]["nom"],
                "sous_domaines" => $sousDomainesDomaines,
            ];

            $data['cruds'] = $auth_actions;
        }


        return $this->setJsonResponse($data);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index() {
        $this->paginate = [
            'contain' => ['Services'],
        ];
        $domaines = $this->paginate($this->Domaines);

        $this->set(compact('domaines'));
    }

    /**
     * View method
     *
     * @param string|null $id Domaine id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null) {
        $domaine = $this->Domaines->get($id, [
            'contain' => ['Services'],
        ]);

        $this->set(compact('domaine'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $domaine = $this->Domaines->newEmptyEntity();
        if ($this->request->is('post')) {
            //$domaine = $this->Domaines->patchEntity($domaine, $this->request->getData());
            $domaine->nom = $this->request->getData('nom');

            $domaine->Services_id = $this->request->getData('Services_id');
            $service = $this->Domaines->Services->get($this->request->getData('Services_id'));
            $domaine->Services_Fournisseurs_id = $service->Fournisseurs_id;

            //$domaine->Services_id = 2;
            //$domaine->Services_Fournisseurs_id = 3;


            Log::debug(print_r($this->request->getData(), true));
            Log::debug(print_r($domaine, true));

            if ($this->Domaines->save($domaine)) {
                $this->Flash->success(__('The domaine has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The domaine could not be saved. Please, try again.'));
        }
        $services = $this->Domaines->Services->find('list', ['limit' => 200])->all();

        //liste des services_id [];
        //listes des fournisseurs_id
        $this->set(compact('domaine', 'services'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Domaine id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null) {
        $domaine = $this->Domaines->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $domaine = $this->Domaines->patchEntity($domaine, $this->request->getData());
            if ($this->Domaines->save($domaine)) {
                $this->Flash->success(__('The domaine has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The domaine could not be saved. Please, try again.'));
        }
        $services = $this->Domaines->Services->find('list', ['limit' => 200])->all();
        $this->set(compact('domaine', 'services'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Domaine id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $domaine = $this->Domaines->get($id);
        if ($this->Domaines->delete($domaine)) {
            $this->Flash->success(__('The domaine has been deleted.'));
        } else {
            $this->Flash->error(__('The domaine could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}
