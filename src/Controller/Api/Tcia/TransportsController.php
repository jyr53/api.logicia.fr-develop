<?php

declare(strict_types=1);

namespace App\Controller\Api\Tcia;

use App\Controller\AppController;
use App\Controller\Traits\ResponseTrait;
use Cake\Log\Log;
use Cake\Http\Client;
use mikehaertl\pdftk\Pdf;
use Cake\I18n\FrozenTime;

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
        //Récupération de toutes les actions (fonctions) possibles pour les Domaines
        $all_actions = $this->ApiData->getActions($this->request->getAttribute('params')['controller'], $this->request->getAttribute('params')['prefix']);

        $transports = $this->Transports->getAllTransports()->toArray();


        //Tableau de données retourné vers vuejs - tableau final
        $data = [];
        $data['items'] = [];
        foreach ($transports as $transport) {
            $data['items'][] = [
                "num_client" => $transport->num_client,
                "nom_contact" => $transport->nom_contact,
                "tel_contact" => $transport->tel_contact,
                "email_contact" => $transport->email_contact,
                "agence_depart_aller_id" => $transport->agences1['nom'],
                "agence_arrivee_aller_id" => $transport->agences2['nom'],
                "agence_depart_retour_id" => isset($transport->agences3['nom']) ? $transport->agences3['nom'] : "",
                "agence_arrivee_retour_id" => isset($transport->agences4['nom']) ? $transport->agences4['nom'] : "",
                "contenu" => $transport->contenu,
                "motif" => $transport->motif,
                "nb_colis" => $transport->nb_colis,
                "date_depart_aller" => $transport->date_depart_aller,
                "date_arrivee_aller" => $transport->date_arrivee_aller,
                "date_depart_retour" => $transport->date_depart_retour,
                "date_arrivee_retour" => $transport->date_arrivee_retour,
                "intervention_id" => $transport->intervention_id,
                "expediteur" => $transport->expediteur,
                "id" => $transport->id,

            ];
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
        $this->autoRender = false; // pour ne pas rendre une vue (template) cake
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


        $transport = $this->Transports->newEmptyEntity();
        if ($this->request->is('post')) { //verification pour le post 

            $transport = $this->Transports->patchEntity($transport, $this->request->getData()["insertTransport"]);
            //creation d'un tiket pour prise en charge technicien
            // Log::debug(print_r($transport, true));
            // $intervention["customer_id"] = $transport["num_client"];
            // $intervention["contact_notif"] = $transport["email_contact"];
            // $intervention["contact"] = $transport["tel_contact"] . " " . $transport["nom_contact"];
            // $intervention["objet"] = $transport["motif"];
            // $http = new Client();
            // $response = $http->post('https://sav.logicia.fr/osii-ace/wsinterventions/create', ['Intervention' => $intervention]);

            $interventionId = 6; //$response->getJson()["message"];       
            $error = 1;

            if ($interventionId > 0) :

                $transport->intervention_id = $interventionId;

                if ($this->Transports->save($transport)) :
                    $error = 0;
                endif;
            endif;

            // prepare la requete pour l adresse
            $http = new Client([
                'scheme' => 'https',
                'ssl' => [
                    'verify_peer' => false,
                    'verify_host' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]); // va cherche ladresse
            $URLprepar = "https://sav.logicia.fr/osii-ace/wscustomers/view/" . ($this->request->getData()['forPDF']['idClient'] . '.json');
            $CustomerResponse = $http->get($URLprepar);
            $json = $CustomerResponse->getJson();

            // prepare le pdf
            $pdf = new Pdf(WWW_ROOT . '/pdf/TciaFormPDFVierge.pdf');
            $result = $pdf->fillForm([
                'expe' => $this->request->getData()['forPDF']['expe'] ? $this->request->getData()['forPDF']['expe'] : '',
                'adh_coor_adr1' => $json['customer']['Customer']['Civility'] . ' ' . $json['customer']['Customer']['Name'] ? $json['customer']['Customer']['Civility'] . ' ' . $json['customer']['Customer']['Name'] : '',
                'adh_coor_adr2' => $json['customer']['Customer']['MainDeliveryAddress_Address1'] ? $json['customer']['Customer']['MainDeliveryAddress_Address1'] : '',
                'adh_coor_cp_ville' => $json['customer']['Customer']['MainDeliveryAddress_ZipCode'] && $json['customer']['Customer']['MainDeliveryAddress_City'] ? $json['customer']['Customer']['MainDeliveryAddress_ZipCode'] . ' ' . $json['customer']['Customer']['MainDeliveryAddress_City'] : '',
                'adh_coor_telephone' => $this->request->getData()['forPDF']['telephone'] ? $this->request->getData()['forPDF']['telephone'] : '',
                'colis_content' => $this->request->getData()['forPDF']['contenu'] ? $this->request->getData()['forPDF']['contenu'] : '',
                'motif' => $this->request->getData()['forPDF']['motif'] ? $this->request->getData()['forPDF']['motif'] : '',
                'date_envoi' => $this->request->getData()['forPDF']['date_envoi'] ? $this->request->getData()['forPDF']['date_envoi'] : '',
                'nom_secretaire' => $this->request->getData()['forPDF']['expediteur'] ? $this->request->getData()['forPDF']['expediteur'] : '',
                'nom_transporteur' => $this->request->getData()['forPDF']['transporteur'] ? $this->request->getData()['forPDF']['transporteur'] : '',
                'destinataire' => $this->request->getData()['forPDF']['destinataire'] ? $this->request->getData()['forPDF']['destinataire'] : '',
                'destinataire_adr1' => $this->request->getData()['forPDF']['destinataire_adr1'] ? $this->request->getData()['forPDF']['destinataire_adr1'] : '',
                'destinataire_adr2' => $this->request->getData()['forPDF']['destinataire_adr2'] ? $this->request->getData()['forPDF']['destinataire_adr2'] : '',
                'destinataire_CP_Ville' => $this->request->getData()['forPDF']['destinataire_CP_Ville']
            ])->needAppearances()
                ->saveAs(WWW_ROOT . '/pdf/filled.pdf');
            //renvoie le pdf constitué
            $this->response = $this->response->withType('application/pdf');
            $this->response = $this->response->withFile(WWW_ROOT . '/pdf/filled.pdf')

                ->cors($this->request)
                ->allowOrigin(['*'])
                ->allowMethods(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'])
                ->allowHeaders(['X-CSRF-Token', '*'])
                ->allowCredentials()
                ->exposeHeaders(['Link'])
                ->maxAge(300)
                ->build();
            return $this->response;
        }
    }
        /**
     * update  method
     *permet de mettre a jour les dates arrivé et depart selon le statut
     * @return 
     */
    public function update($id)
    {
        $this->autoRender = false; // pour ne pas rendre une vue (template) cake
        $transport = $this->Transports
            ->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $statut = $this->request->getData()['etat'];

            $time = new FrozenTime;//prepare la date du jour pour la bdd
            if ($statut == 1) {
                $transport->date_arrivee_aller = $time;
            }
            if ($statut == 2) {
                $transport->date_depart_retour = $time;
            }
            if ($statut == 3) {
                $transport->date_arrivee_retour = $time;
            }
            if ($this->Transports->save($transport)) {//sauvegarde en bdd
                return $this->setJsonResponse('la date a été sauvegardé.');
            }
            return $this->setJsonResponse('la date n\'as pas été sauvegardé.');
        }
    }
}
