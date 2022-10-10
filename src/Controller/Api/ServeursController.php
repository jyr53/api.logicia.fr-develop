<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Controller\Traits\ResponseTrait;
use Cake\Log\Log;
use CakeDC\Auth\Rbac\Rbac;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Serveurs Controller
 *
 * @property \App\Model\Table\ServeursTable $Serveurs
 * @method \App\Model\Entity\Serveur[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ServeursController extends AppController {

//Chargement de la fonction pour le formatage de la réponse en json
    use ResponseTrait;

    /*
     * Fonction d'initialisation
     */

    public function initialize(): void {
        parent::initialize();

//Chargement des modèles liés à la talbe Serveurs
        $this->Services = $this->loadModel('Services');
        $this->SystemesExploitation = $this->loadModel('SystemesExploitation');
        $this->Sauvegardes = $this->loadModel('Sauvegardes');
        $this->ServeursHasComptes = $this->loadModel('ServeursHasComptes');

//Chargement du component ApiData
        $this->loadComponent('ApiData', ['blackholeCallback' => 'blackhole']);
    }

    /*
     * Fonction List : Listes tous les serveurs et leurs dépendances ou enfants.
     * return $data
     */

    public function list() {

        $this->autoRender = false; // pour ne pas rendre une vue (template) cake
//
//
//Récupération de toutes les actions (fonctions) possibles pour les serveurs
        $all_actions = $this->ApiData->getActions($this->request->getAttribute('params')['controller'], $this->request->getAttribute('params')['prefix']);

// Obtenir l'identity à partir de la requête
        $user = $this->request->getAttribute('identity');

        $rbac = new Rbac();
        $auth_actions = [];
        foreach ($all_actions['Serveurs'] as $action) {
            $request = new ServerRequest('GET', '');
            $request->withAttribute('params', [
                'prefix' => 'Api',
                'plugin' => null,
                'extension' => null,
                'controller' => 'Serveurs',
                'action' => $action,
                'role' => 'superuser',
            ]);
            if ($rbac->checkPermissions($user, $request)):
                $tmp = [];
                switch ($action):
                    case "add":
                        $tmp[] = ["nom" => "Ajouter", "icon" => "mdi-plus", "visible" => false];
                        break;
                    case "view":
                        $tmp[] = ["nom" => "Voir", "icon" => "mdi-eye", "visible" => true];
                        break;
                    case "edit":
                        $tmp[] = ["nom" => "Modifier", "icon" => "mdi-pencil", "visible" => true];
                        break;
                    case "delete":
                        $tmp[] = ["nom" => "Supprimer", "icon" => "mdi-close", "visible" => true];
                        break;
                    default:
                        $tmp[] = ["nom" => $action, "icon" => "", "visible" => false];
                        break;
                endswitch;
            endif;
            array_push($auth_actions, $tmp);
        }

//Recupere tous les serveurs depuis une fonction du model Serveur
        $serveurs = $this->Serveurs->getAllServeurs()->toArray();

//Tableau de données retourné vers vuejs - tableau final
        $data = [];

//Parcours tous mes serveurs
        foreach ($serveurs as $serveur) :

            $filtres_services = ['Services.id' => $serveur->Services_id];
            $filtres_sys_exploitation = ['id' => $serveur->Systemes_exploitation_id];

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

        endforeach;

        $data['cruds'] = $auth_actions;

        $data["headers"] = [
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Nom",
                "value" => "nom",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Client",
                "value" => "nom_client",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Code client",
                "value" => "code_client",
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
                "text" => "Systeme",
                "value" => "sys_exploitation",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "IP",
                "value" => "IP",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Infos",
                "value" => "infos_tech",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Hebergeur",
                "value" => "fournisseur",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Reference fournisseur",
                "value" => "reference_fournisseur",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Login Admin",
                "value" => "login_admin",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => false,
                "searchable" => false,
                "text" => "Mdp admin",
                "value" => "mdp_admin",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Login FTP",
                "value" => "login_ftp",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => false,
                "searchable" => false,
                "text" => "Mdp FTP",
                "value" => "mfp_ftp",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Login SSH",
                "value" => "login_ssh",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => false,
                "searchable" => false,
                "text" => "Mdp SSH",
                "value" => "mdp_ssh",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Login BDD",
                "value" => "login_bdd",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => false,
                "searchable" => false,
                "text" => "Mdp BDD",
                "value" => "mdp_bdd",
                "divider" => true
            ],
            [
                "visible" => true,
                "sortable" => false,
                "searchable" => false,
                "text" => "Actions",
                "value" => "actions",
                "divider" => true
            ],
        ];

        $data["sort_by"] = "serveur_id";
        $data["sort_desc"] = false;
        $data["items_per_page"] = 10;

        return $this->setJsonResponse($data);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index() {
        $this->paginate = [
            'contain' => ['Services', 'SystemesExploitation', 'Sauvegardes'],
        ];
        $serveurs = $this->paginate($this->Serveurs);

        $this->set(compact('serveurs'));
    }

    /**
     * View method
     *
     * @param string|null $id Serveur id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null) {
        $serveur = $this->Serveurs->get($id, [
            'contain' => ['Services', 'SystemesExploitation', 'Sauvegardes'],
        ]);

        $this->set(compact('serveur'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $serveur = $this->Serveurs->newEmptyEntity();
        if ($this->request->is('post')) {
            $serveur = $this->Serveurs->patchEntity($serveur, $this->request->getData());

            $serveur->Services_id = $this->request->getData('Services_id');

            $service = $this->Serveurs->Services->get($this->request->getData('Services_id'));

            $serveur->Services_Fournisseurs_id = $service->Fournisseurs_id;

            if ($this->Serveurs->save($serveur)) {
                $this->Flash->success(__('The serveur has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The serveur could not be saved. Please, try again.'));
        }

        $services = $this->Serveurs->Services->find('list', ['limit' => 200])->all();
        $systemesExploitation = $this->Serveurs->SystemesExploitation->find('list', ['limit' => 200])->all();
        $sauvegardes = $this->Serveurs->Sauvegardes->find('list', ['limit' => 200])->all();
        $this->set('type', $this->Serveurs->enum('type'));
        $this->set(compact('serveur', 'services', 'systemesExploitation', 'sauvegardes'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Serveur id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null) {
        $serveur = $this->Serveurs->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $serveur = $this->Serveurs->patchEntity($serveur, $this->request->getData());
            if ($this->Serveurs->save($serveur)) {
                $this->Flash->success(__('The serveur has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The serveur could not be saved. Please, try again.'));
        }
        $services = $this->Serveurs->Services->find('list', ['limit' => 200])->all();
        $systemesExploitation = $this->Serveurs->SystemesExploitation->find('list', ['limit' => 200])->all();
        $sauvegardes = $this->Serveurs->Sauvegardes->find('list', ['limit' => 200])->all();
        $this->set('type', $this->Serveurs->enum('type'));
        $this->set(compact('serveur', 'services', 'systemesExploitation', 'sauvegardes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Serveur id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $serveur = $this->Serveurs->get($id);
        if ($this->Serveurs->delete($serveur)) {
            $this->Flash->success(__('The serveur has been deleted.'));
        } else {
            $this->Flash->error(__('The serveur could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}
