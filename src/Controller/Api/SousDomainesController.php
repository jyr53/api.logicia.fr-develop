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
 * @property \App\Model\Table\SousDomainesTable $Sous_Domaines
 * @method \App\Model\Entity\Sous_Domaines[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SousDomainesController extends AppController {

    //Chargement de la fonction pour le formatage de la réponse en json
    use ResponseTrait;

    /*
     * Fonction d'initialisation
     */

    public function initialize(): void {
        parent::initialize();

        //Chargement des modèles liés à la talbe Sous_Domaines
        $this->Domaines = $this->loadModel('Domaines');
        $this->Websites = $this->loadModel('Websites');

        //Chargement du component ApiData
        $this->loadComponent('ApiData', ['blackholeCallback' => 'blackhole']);
    }

    /*
     * Fonction List : Listes tous les Sous_Domaines et leurs dépendances ou enfants.
     * return $data
     */

    public function list() {

        $this->autoRender = false; // pour ne pas rendre une vue (template) cake
        //
        //
        //Récupération de toutes les actions (fonctions) possibles pour les Sous_Domaines
        $all_actions = $this->ApiData->getActions($this->request->getAttribute('params')['controller'], $this->request->getAttribute('params')['prefix']);

        // Obtenir l'identity à partir de la requête
        $user = $this->request->getAttribute('identity');

        $rbac = new Rbac();
        $auth_actions = [];
        foreach ($all_actions['Sous_Domaines'] as $action) {
            $request = new ServerRequest('GET', '');
            $request->withAttribute('params', [
                'prefix' => 'Api',
                'plugin' => null,
                'extension' => null,
                'controller' => 'Sous_Domaines',
                'action' => $action,
                'role' => 'superuser',
            ]);
            if ($rbac->checkPermissions($user, $request))
                array_push($auth_actions, $action);
        }

        //Recupere tous les Sous_Domaines depuis une fonction du model Sous_omaines
        $sousDomaines = $this->SousDomaines->getAllSousDomaine()->toArray();
        
        //Tableau de données retourné vers vuejs - tableau final
        $data = [];

        //Parcours tous mes Sous_Domaines
        foreach ($sousDomaines as $sousDomaine) {

            $filtres_services = ['Sous_Domaines.id' => $sousDomaine->Sous_Domaines_id];
            $filtres_sys_exploitation = ['id' => $sousDomaine->Systemes_exploitation_id];

            //Récupération du service du serveur en cours dans le foreach
            $service = $this->Services->getServiceAndFournisseur($filtres_services)->toArray();

            //Récupération du systeme d'exploitation du serveur en cours dans le foreach
            $sys_exploitation = $this->SystemesExploitation->getSystemeExploitation($filtres_sys_exploitation)->toArray();

            

            //Récupération de tous les comptes liés au serveur en cours dans le foreach  - tableau
            $serveurs_has_comptes = $this->ServeursHasComptes->getAllComptesByServerID($serveur->id)->toArray();
            $comptesServeurs = [];

            foreach ($serveurs_has_comptes as $serveur_has_compte):
                $comptesServeurs[] = [
                    "is_root" => $serveur_has_compte->is_root,
                    "login" => $serveur_has_compte->compte["login"],
                    "commentaire" => $serveur_has_compte->compte["commentaire"],
                    "type" => $serveur_has_compte->compte["type"]["value"],
                    "lockself" => $serveur_has_compte->compte["lockself"],
                    "adresse" => $serveur_has_compte->compte["adresse"],
                ];
            endforeach;

            //Log::debug(print_r($service, true));

            $data['items'][] = [
                "serveur_id" => $serveur->id,
                "services_id" => $serveur->Services_id,
                "fournisseurs_id" => $service[0]["Fournisseurs_id"],
                "nom" => $serveur->nom,
                "IP" => $serveur->IP,
                "infos_tech" => $serveur->infos_tech,
                "type" => $serveur->type->value,
                "sys_exploitation" => $sys_exploitation[0]["nom"],
                "code_client" => $service[0]["code_client"],
                "nom_client" => $service[0]["nom_client"],
                "renouvellement" => $service[0]["renouvellement"],
                "assistance" => ($service[0]["contrat_assistance"] == 1) ? "OUI" : "NON",
                "reference_fournisseur" => $service[0]["reference_fournisseur"],
                "fournisseur" => $service[0]["fournisseur"]["nom"],
                "comptes" => $comptesServeurs
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
    public function index()
    {
        $this->paginate = [
            'contain' => ['Domaines', 'Websites'],
        ];
        $sousDomaines = $this->paginate($this->SousDomaines);

        $this->set(compact('sousDomaines'));
    }

    /**
     * View method
     *
     * @param string|null $id Sous Domaine id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $sousDomaine = $this->SousDomaines->get($id, [
            'contain' => ['Domaines', 'Websites'],
        ]);

        $this->set(compact('sousDomaine'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $sousDomaine = $this->SousDomaines->newEmptyEntity();
        if ($this->request->is('post')) {
           $sousDomaine = $this->SousDomaines->patchEntity($sousDomaine, $this->request->getData());
            $sousDomaine->Domaines_id = $this->request->getData('Domaines_id');
            
            
            
            $sousDomaine->Domaines_id = $this->request->getData('Domaines_id');

            $domaine = $this->SousDomaines->Domaines->get($this->request->getData('Domaines_id'));

            $sousDomaine->Domaines_Services_id = $domaine->Services_id;
            $sousDomaine->Domaines_Services_Fournisseurs_id = $domaine->Services_Fournisseurs_id;

            $sousDomaine->Websites_id = $this->request->getData('Websites_id');

            $website = $this->SousDomaines->Websites->get($this->request->getData('Websites_id'));

            $sousDomaine->Websites_Services_id = $website->Services_id;
            $sousDomaine->Websites_Services_Fournisseurs_id = $website->Services_Fournisseurs_id;


            /*$sousDomaine->Services_id = $this->request->getData('Services_id');

            $websites = $this->Websites->Services->get($this->request->getData('Services_id'));

            $sousDomaine->Services_Fournisseurs_id = $service->Fournisseurs_id;*/


            Log::debug(print_r($this->request->getData(), true));
            Log::debug(print_r($domaine, true));


            if ($this->SousDomaines->save($sousDomaine)) {
                $this->Flash->success(__('The sous domaine has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The sous domaine could not be saved. Please, try again.'));
        }
        $domaines = $this->SousDomaines->Domaines->find('list', ['limit' => 200])->all();
        $websites = $this->SousDomaines->Websites->find('list', ['limit' => 200])->all();
        $this->set(compact('sousDomaine', 'domaines', 'websites'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Sous Domaine id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $sousDomaine = $this->SousDomaines->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $sousDomaine = $this->SousDomaines->patchEntity($sousDomaine, $this->request->getData());
            if ($this->SousDomaines->save($sousDomaine)) {
                $this->Flash->success(__('The sous domaine has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The sous domaine could not be saved. Please, try again.'));
        }
        $domaines = $this->SousDomaines->Domaines->find('list', ['limit' => 200])->all();
        $websites = $this->SousDomaines->Websites->find('list', ['limit' => 200])->all();
        $this->set(compact('sousDomaine', 'domaines', 'websites'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Sous Domaine id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $sousDomaine = $this->SousDomaines->get($id);
        if ($this->SousDomaines->delete($sousDomaine)) {
            $this->Flash->success(__('The sous domaine has been deleted.'));
        } else {
            $this->Flash->error(__('The sous domaine could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}
