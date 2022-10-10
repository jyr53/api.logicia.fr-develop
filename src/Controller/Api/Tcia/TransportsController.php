<?php

declare(strict_types=1);

namespace App\Controller\Api\Tcia;

use App\Controller\AppController;
use App\Controller\Traits\ResponseTrait;
use Cake\Log\Log;
use CakeDC\Auth\Rbac\Rbac;
use Cake\Http\ServerRequest;
use Cake\Http\Client;
use Cake\Http\Response;

use mikehaertl\pdftk\Pdf;

/**
 * Transports Controller
 *
 * @property \App\Model\Table\TransportsTable $Transports
 * @method \App\Model\Entity\Transport[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TransportsController extends AppController
{

    //Chargement de la fonction pour le formatage de la réponse en json
    use ResponseTrait;

    /*
     * Fonction d'initialisation
     */

    public function initialize(): void
    {
        parent::initialize();

        //Chargement des modèles liés à la talbe Domaines
        $this->Transports = $this->loadModel('Transports');
        $this->Agences = $this->loadModel('Agences');

        //Chargement du component ApiData
        $this->loadComponent('ApiData', ['blackholeCallback' => 'blackhole']);
    }

    public function list()
    {

        $this->autoRender = false; // pour ne pas rendre une vue (template) cake
        //
        //
        //Récupération de toutes les actions (fonctions) possibles pour les Domaines
        $all_actions = $this->ApiData->getActions($this->request->getAttribute('params')['controller'], $this->request->getAttribute('params')['prefix']);

        //Création table users - attention taille du token
        //bake model users
        //bake controller users
        //bake template users - crud
        //ajouter un user
        //Construire requete en vue vers l'api
        //Peut etre ajouter la route dans routes.php
        //Verif si email envoyé par vue existe dans table user
        //SI oui alors récup token et stocker
        //Sinon pas le droit et envoyé non autorisation à vue


        // -----------------------------------------------------

        // Obtenir l'identity à partir de la requête
        //$user = $this->request->getAttribute('identity');

        /*$rbac = new Rbac();
        $auth_actions = [];
        foreach ($all_actions['Transports'] as $action) {
            $request = new ServerRequest();
            $request->withAttribute('params', [
                'prefix' => 'Api',
                'plugin' => null,
                'extension' => null,
                'controller' => 'Transports',
                'action' => $action,
                'role' => 'superuser',
            ]);
            $tmp = array();
            if ($rbac->checkPermissions($user, $request)) :
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
            array_push($auth_actions, $tmp);*/
        }

        //Recupere tous les Domaines depuis une fonction du model Domaine
        $transports = $this->Transports->getAllTransports()->toArray();

        //Log::debug(print_r($transports, true));
        //Tableau de données retourné vers vuejs - tableau final
        $data = [];

        $data['cruds'] = $auth_actions;
        $data['items'] = [];

        //Parcours tous mes Domaines
        foreach ($transports as $transport) {


            switch ($transport->etat):
                case 0:
                    $transport->etat = "Non initialisé";
                    break;
                case 1:
                    $transport->etat = "Départ aller";
                    break;
                case 2:
                    $transport->etat = "Arrivée aller";
                    break;
                case 3:
                    $transport->etat = "Départ retour";
                    break;
                case 4:
                    $transport->etat = "Arrivée retour";
                    break;
            endswitch;

            $data['items'][] = [
                "num_client" => $transport->num_client,
                "nom_contact" => $transport->nom_contact,
                "tel_contact" => $transport->tel_contact,
                "email_contact" => $transport->email_contact,
                "agence_depart_aller_id" => $transport->agences1['nom'],
                "agence_arrivee_aller_id" => $transport->agences2['nom'],
                "agence_depart_retour_id" => isset($transport->agences3['nom']) ? $transport->agences3['nom'] : "",
                "agence_arrivee_retour_id" => isset($transport->agences4['nom']) ? $transport->agences4['nom'] : "",
                "date_depot" => $transport->date_depot,
                "contenu" => $transport->contenu,
                "motif" => $transport->motif,
                "nb_colis" => $transport->nb_colis,
                "date_depart_aller" => $transport->date_depart_aller,
                "date_arrivee_aller" => $transport->date_arrivee_aller,
                "date_depart_retour" => $transport->date_depart_retour,
                "date_arrivee_retour" => $transport->date_arrivee_retour,
                "etat" => $transport->etat,
                "intervention_id" => $transport->intervention_id,
                "expediteur" => $transport->expediteur,
                "actions" => '',
                "id" => $transport->id
            ];

            $data['cruds'] = $auth_actions;
        }

        //Header datatable
        $data["headers"] = [
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "N° client",
                "value" => "num_client",
                "divider" => true,
                "filterable" => false
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Nom du contact",
                "value" => "nom_contact",
                "divider" => true,
                "filterable" => false
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Tel du contact",
                "value" => "tel_contact",
                "divider" => true,
                "filterable" => false
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Email du contact",
                "value" => "email_contact",
                "divider" => true,
                "filterable" => false
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Agence de départ Aller",
                "value" => "agence_depart_aller_id",
                "divider" => true,
                "filterable" => true
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Agence d'arrivée Aller",
                "value" => "agence_arrivee_aller_id",
                "divider" => true,
                "filterable" => true
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Agence de départ Retour",
                "value" => "agence_depart_retour_id",
                "divider" => true,
                "filterable" => false
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Agence d'arrivée Retour",
                "value" => "agence_arrivee_retour_id",
                "divider" => true,
                "filterable" => false
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Date de dépot",
                "value" => "date_depot",
                "divider" => true,
                "filterable" => false
            ],
            [
                "visible" => false,
                "sortable" => false,
                "searchable" => false,
                "text" => "Contenu",
                "value" => "contenu",
                "divider" => true,
                "filterable" => false
            ],
            [
                "visible" => false,
                "sortable" => false,
                "searchable" => false,
                "text" => "Motif",
                "value" => "motif",
                "divider" => true,
                "filterable" => false
            ],
            [
                "visible" => true,
                "sortable" => false,
                "searchable" => false,
                "text" => "Nombre de colis",
                "value" => "nb_colis",
                "divider" => true,
                "filterable" => false
            ],
            [
                "visible" => false,
                "sortable" => false,
                "searchable" => false,
                "text" => "Date de départ aller",
                "value" => "date_depart_aller",
                "divider" => true,
                "filterable" => false
            ],
            [
                "visible" => false,
                "sortable" => false,
                "searchable" => false,
                "text" => "Date d'arrivée aller",
                "value" => "date_arrivee_aller",
                "divider" => true,
                "filterable" => false
            ],
            [
                "visible" => false,
                "sortable" => false,
                "searchable" => false,
                "text" => "Date de départ retour",
                "value" => "date_depart_retour",
                "divider" => true,
                "filterable" => false
            ],
            [
                "visible" => false,
                "sortable" => false,
                "searchable" => false,
                "text" => "Date d'arrivée retour",
                "value" => "date_arrivee_retour",
                "divider" => true,
                "filterable" => false
            ],
            [
                "visible" => true,
                "sortable" => true,
                "searchable" => true,
                "text" => "Etat",
                "value" => "etat",
                "divider" => true,
                "filterable" => false
            ],
            [
                "visible" => false,
                "sortable" => false,
                "searchable" => false,
                "text" => "N° d'intervention",
                "value" => "intervention_id",
                "divider" => true,
                "filterable" => false
            ],
            [
                "visible" => false,
                "sortable" => false,
                "searchable" => false,
                "text" => "Expediteur",
                "value" => "expediteur",
                "divider" => true,
                "filterable" => false
            ],
            [
                "visible" => true,
                "sortable" => false,
                "searchable" => false,
                "text" => "Actions",
                "value" => "actions",
                "divider" => true,
                "filterable" => false
            ],
            [
                "visible" => false,
                "sortable" => false,
                "searchable" => false,
                "text" => "Id",
                "value" => "id",
                "divider" => true,
                "filterable" => false
            ],
        ];

        $data["sort_by"] = "etat";
        $data["sort_desc"] = false;
        $data["items_per_page"] = 50;

        return $this->setJsonResponse($data);
    }

    /**
     * View method
     *
     * @param string|null $id Transport id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $transport = $this->Transports->get($id, [
            'contain' => ['Agences'],
        ]);

        $this->set(compact('transport'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {

        $this->autoRender = false; // pour ne pas rendre une vue (template) cake

        /* $transport = $this->Transports->newEmptyEntity();
        if ($this->request->is('post')) {

            //Log::debug(print_r($this->request->getData(), true));
            $transport = $this->Transports->patchEntity($transport, $this->request->getData());

            $intervention["customer_id"] = $transport["num_client"];
            $intervention["contact_notif"] = $transport["email_contact"];
            $intervention["contact"] = $transport["tel_contact"] . " " . $transport["nom_contact"];
            $intervention["objet"] = $transport["motif"];

            //Log::debug(print_r($intervention, true));

            $http = new Client();
            $response = $http->post('https://sav.logicia.fr/osii-ace/wsinterventions/create', ['Intervention' => $intervention]);

            //Log::debug(print_r($response->getJson()["message"], true));

            $interventionId = $response->getJson()["message"];

            //Log::debug(print_r($interventionId, true));

            $error = 1;

            if ($interventionId > 0):
                $transport->etat = 1;
                $transport->intervention_id = $interventionId;
                $transport->date_depot = date("Y-m-d");

                if ($this->Transports->save($transport)) :
                    $error = 0;
                endif;
            endif;

            return $this->setJsonResponse($error);
        } */

        $pdf = new Pdf();
        $pdf->addFile('/path/to/file1.pdf');
        $pdf->fillForm(array(
            'expe' => 'patate',
            'adh_coor' => 'patate',
            'colis_content' => 'patate',
            'motif' => 'patate',
            'date_envoi' => 'patate',
            'nom_secretaire' => 'patate',
            'nom_transporteur' => 'patate',
            'destinataire' => 'patate'
        ));

        return $this->setJsonResponse(array('bonsoir' => 'bissoir'));
    }

    /**
     * increaseEtat method
     *
     * @param string|null $id Transport id.
     * @return \Cake\Http\Response|null|void Redirects on successful increaseEtat, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function increaseetat($id = null)
    {

        $this->autoRender = false;

        $transport = $this->Transports->get(intval($id));
        $data = [];
        if ($this->request->is(['patch', 'post', 'put', 'get'])) {
            if ($transport->etat === 3) {
                $transport->etat = 4;
                $transport->date_arrivee_retour = date("Y-m-d");
                array_push($data, ['message' => "Modification opérée"]);
            } elseif ($transport->etat === 2) {
                $transport->etat = 3;
                $transport->date_depart_retour = date("Y-m-d");
                array_push($data, ['message' => "Modification opérée"]);
            } elseif ($transport->etat === 1) {
                $transport->etat = 2;
                $transport->date_arrivee_aller = date("Y-m-d");
                array_push($data, ['message' => "Modification opérée"]);
            } elseif ($transport->etat === 0) {
                $transport->etat = 1;
                $transport->date_depart_aller = date("Y-m-d");
                array_push($data, ['message' => "Modification opérée"]);
            } else {
                array_push($data, ['message' => "Le(s) coli(s) est(sont) déjà arrivé(s) à destination"]);
            }
            if ($this->Transports->save($transport)) {
                return $this->setJsonResponse($data);
            } else {
                return $this->setJsonResponse(['error' => 'Une erreur est survenue lors de la mise à jour de l\'état du transport.']);
            };
        }
    }

    // /**
    //  * Edit method
    //  *
    //  * @param string|null $id Transport id.
    //  * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
    //  * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
    //  */
    // public function edit($id = null) {
    //     $transport = $this->Transports->get($id, [
    //         'contain' => [],
    //     ]);
    //     if ($this->request->is(['patch', 'post', 'put'])) {
    //         $transport = $this->Transports->patchEntity($transport, $this->request->getData());
    //         if ($this->Transports->save($transport)) {
    //             $this->Flash->success(__('The transport has been saved.'));

    //             return $this->redirect(['action' => 'index']);
    //         }
    //         $this->Flash->error(__('The transport could not be saved. Please, try again.'));
    //     }
    //     $agences = $this->Transports->Agences->find('list', ['limit' => 200])->all();
    //     $interventions = $this->Transports->Interventions->find('list', ['limit' => 200])->all();
    //     $this->set(compact('transport', 'agences', 'interventions'));
    // }

    // /**
    //  * Delete method
    //  *
    //  * @param string|null $id Transport id.
    //  * @return \Cake\Http\Response|null|void Redirects to index.
    //  * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
    //  */
    // public function delete($id = null) {
    //     $this->request->allowMethod(['post', 'delete']);
    //     $transport = $this->Transports->get($id);
    //     if ($this->Transports->delete($transport)) {
    //         $this->Flash->success(__('The transport has been deleted.'));
    //     } else {
    //         $this->Flash->error(__('The transport could not be deleted. Please, try again.'));
    //     }

    //     return $this->redirect(['action' => 'index']);
    // }

}
