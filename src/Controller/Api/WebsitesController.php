<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Controller\Traits\ResponseTrait;
use Cake\Log\Log;
use CakeDC\Auth\Rbac\Rbac;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Websites Controller
 *
 * @property \App\Model\Table\WebsitesTable $Websites
 * @method \App\Model\Entity\Website[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class WebsitesController extends AppController {

    //Chargement de la fonction pour le formatage de la réponse en json
    use ResponseTrait;

    /*
     * Fonction d'initialisation
     */

    public function initialize(): void {
        parent::initialize();

        //Chargement des modèles liés à la talbe Websites
        $this->Services = $this->loadModel('Services');
        $this->Serveurs = $this->loadModel('Serveurs');
        $this->Sauvegardes = $this->loadModel('Sauvegardes');
        $this->Domaines = $this->loadModel('Domaines');
        $this->SousDomaines = $this->loadModel('SousDomaines');
        $this->WebsitesHasComptes = $this->loadModel('WebsitesHasComptes');

        //Chargement du component ApiData
        $this->loadComponent('ApiData', ['blackholeCallback' => 'blackhole']);
    }

    /*
     * Fonction List : Listes tous les websites et leurs dépendances ou enfants.
     * return $data
     */

    public function list() {

        $this->autoRender = false; // pour ne pas rendre une vue (template) cake
        //
        //
        //Récupération de toutes les actions (fonctions) possibles pour les websites
        $all_actions = $this->ApiData->getActions($this->request->getAttribute('params')['controller'], $this->request->getAttribute('params')['prefix']);

        // Obtenir l'identity à partir de la requête
        $user = $this->request->getAttribute('identity');

        $rbac = new Rbac();
        $auth_actions = [];
        foreach ($all_actions['Websites'] as $action) {
            $request = new ServerRequest('GET', '');
            $request->withAttribute('params', [
                'prefix' => 'Api',
                'plugin' => null,
                'extension' => null,
                'controller' => 'Websites', 
                'action' => $action,
                'role' => 'superuser',
            ]);
            if ($rbac->checkPermissions($user, $request))
                array_push($auth_actions, $action);
        }

        //Recupere tous les websites depuis une fonction du model Website
        $websites = $this->Websites->getAllWebsites()->toArray();

        //Tableau de données retourné vers vuejs - tableau final
        $data = [];

        //Parcours tous mes websites
        foreach ($websites as $website) {

            $filtres_services = ['Services.id' => $website->Services_id];
            $filtres_serveurs = ['Serveurs.id' => $website->Serveurs_id];
            $filtres_sauvegardes = ['Sauvegardes.id' => $website->Sauvegardes_id];

            //Récupération du service du service et fournisseur en cours dans le foreach
            $service = $this->Services->getServiceAndFournisseur($filtres_services)->toArray();

            //Récupération du service du service et fournisseur en cours dans le foreach
            $sauvegarde = $this->Sauvegardes->getAllSauvegarde($filtres_sauvegardes)->toArray();

            //Récupération du service du serveur en cours dans le foreach
            $serveur = $this->Serveurs->getAllServeur($filtres_serveurs)->toArray();

            //$domaines = $this->Domaines->getAllDomaines($service->id)->toArray();
            //$sys_exploitation = $this->SystemesExploitation->getSystemeExploitation($filtres_sys_exploitation)->toArray();

            //Récupération de tous les comptes liés au serveur en cours dans le foreach  - tableau
            $sousDomaines = $this->SousDomaines->getAllSousDomainesByWebsiteID($website->id)->toArray();
            //Récupération de tous les comptes liés au serveur en cours dans le foreach  - tableau
            $websites_has_comptes = $this->WebsitesHasComptes->getAllComptesByWebsiteID($website->id)->toArray();
            $comptesWebsites = [];
            $sousDomainesWebsites = [];

            foreach ($sousDomaines as $sousDomaine):
                $sousDomainesWebsites[] = [
                    "nom" => $sousDomaine->nom,
                    "sous_domaines_id" => $sousDomaine->id,
                    "domaines_id" => $sousDomaine->Domaines_id,
                ];
            endforeach;

            foreach ($websites_has_comptes as $website_has_compte):
                $comptesWebsites[] = [
                    "compte_id" => $website_has_compte->Comptes_id,
                    "is_root" => $website_has_compte->is_root,
                    "login" => $website_has_compte->compte["login"],
                    "commentaire" => $website_has_compte->compte["commentaire"],
                    "type" => $website_has_compte->compte["type"]["value"], 
                    "lockself" => $website_has_compte->compte["lockself"],
                    "adresse" => $website_has_compte->compte["adresse"],
                ];
            endforeach;

            //Log::debug(print_r($service, true));

            $data['items'][] = [
                "websites_id" => $website->id,
                "services_id" => $website->Services_id,
                "serveurs_id" => $website->Serveurs_id,
                //"domaines_id" => $domaines->getByServiceId($website->Services_id)->domaine_id,
                //"domaines_id" => $domaines[0],
                "sauvegardes_id" => $website->Sauvegardes_id,
                "sauvegardes_services_id" => $website->Sauvegardes_Services_id,
                "sauvegardes_services_fournisseurs_id" => $website->Sauvegardes_Services_Fournisseurs_id,
                "sauvegardes_comptes_id" => $website->Sauvegarde_id,
                "fournisseurs_id" => $service[0]["Fournisseurs_id"],
                "nom" => $serveur[0]["nom"],
                "version" => $website->version,
                "IP" => $website->IP,
                "licence_theme" => $website->licence_theme,
                "plateforme" => $website->plateforme,
                "code_client" => $service[0]["code_client"],
                "nom_client" => $service[0]["nom_client"],
                "renouvellement" => $service[0]["renouvellement"],
                "assistance" => ($service[0]["contrat_assistance"] == 1) ? "OUI" : "NON",
                "reference_fournisseur" => $service[0]["reference_fournisseur"],
                "fournisseur" => $service[0]["fournisseur"]["nom"],
                //"domaines_id" => $domaine[0]["domaines_id"],
                "nom" => $sousDomaines[0]["nom"],
                "comptes" => $comptesWebsites,
                "sous_domaines" => $sousDomainesWebsites,
                "derniere_sauvegarde" => (empty($sauvegarde)) ? "null" : $sauvegarde[0]["derniere"],
                "frequence_sauvegarde" => (empty($sauvegarde)) ? "null" : $sauvegarde[0]["frequence"],
            ];
            //var_dump($websites_has_comptes);

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
            'contain' => ['Services', 'Serveurs', 'Sauvegardes'],
        ];
        $websites = $this->paginate($this->Websites);

        $this->set(compact('websites'));
    }

    /**
     * View method
     *
     * @param string|null $id Website id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null) {
        $website = $this->Websites->get($id, [
            'contain' => ['Services', 'Serveurs', 'Sauvegardes'],
        ]);

        $this->set(compact('website'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $website = $this->Websites->newEmptyEntity();
        if ($this->request->is('post')) {
            $website = $this->Websites->patchEntity($website, $this->request->getData());

//            $website->Services_id = $this->request->getData('Services_id');
//            $website->Services_Fournisseurs_id = $this->request->getData('Services_Fournisseurs_id');
//            $website->Serveurs_id = $this->request->getData('Serveurs_id');
//            $website->Serveurs_Services_id = $this->request->getData('Serveurs_Services_id');
//            $website->Serveurs_Services_Fournisseurs_id = $this->request->getData('Serveurs_Services_Fournisseurs_id');
//            $website->Sauvegardes_id = $this->request->getData('Sauvegardes_id');
//            $website->Sauvegardes_Services_id = $this->request->getData('Sauvegardes_Services_id');
//            $website->Sauvegardes_Services_Fournisseurs_id = $this->request->getData('Sauvegardes_Services_Fournisseurs_id');
//            $website->Sauvegardes_Comptes_id = $this->request->getData('Sauvegardes_Comptes_id');
            $website->Services_id = $this->request->getData('Services_id');

            $service = $this->Websites->Services->get($this->request->getData('Services_id'));

            $website->Services_Fournisseurs_id = $service->Fournisseurs_id;
            
            $website->Serveurs_id = $this->request->getData('Serveurs_id');

            $serveur = $this->Websites->Serveurs->get($this->request->getData('Serveurs_id'));

            $website->Serveurs_Services_id = $serveur->Services_id;

            //$website->Serveurs_id = $this->request->getData('Serveurs_id');

            $service = $this->Websites->Serveurs->Services->get($serveur->Services_id);

            $website->Serveurs_Services_Fournisseurs_id = $service->Fournisseurs_id;

            Log::debug(print_r($this->request->getData(), true));
            Log::debug(print_r($website, true));


            if ($this->Websites->save($website)) {
                $this->Flash->success(__('The website has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The website could not be saved. Please, try again.'));
        }
        $services = $this->Websites->Services->find('list', ['limit' => 200])->all();
        $serveurs = $this->Websites->Serveurs->find('list', ['limit' => 200])->all();
        $sauvegardes = $this->Websites->Sauvegardes->find('list', ['limit' => 200])->all();
        $this->set(compact('website', 'services', 'serveurs', 'sauvegardes'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Website id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null) {
        $website = $this->Websites->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $website = $this->Websites->patchEntity($website, $this->request->getData());
            if ($this->Websites->save($website)) {
                $this->Flash->success(__('The website has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The website could not be saved. Please, try again.'));
        }
        $services = $this->Websites->Services->find('list', ['limit' => 200])->all();
        $serveurs = $this->Websites->Serveurs->find('list', ['limit' => 200])->all();
        $sauvegardes = $this->Websites->Sauvegardes->find('list', ['limit' => 200])->all();
        $this->set(compact('website', 'services', 'serveurs', 'sauvegardes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Website id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $website = $this->Websites->get($id);
        if ($this->Websites->delete($website)) {
            $this->Flash->success(__('The website has been deleted.'));
        } else {
            $this->Flash->error(__('The website could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}
