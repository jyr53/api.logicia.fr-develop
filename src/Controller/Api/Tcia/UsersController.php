<?php

declare(strict_types=1);

namespace App\Controller\Api\Tcia;

use Cake\Core\Configure;
use App\Controller\AppController;
use App\Controller\Traits\ResponseTrait;
use Cake\Event\EventInterface;
use Google;
use Firebase\JWT\JWT;
use Cake\Utility\Security;
use Cake\Log\Log;

require_once 'vendor/autoload.php';
/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

    use ResponseTrait;


    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }
    /**
     * beforefilter 
     * permet actions verifgoogle et login de ce connecter hors authentification
     *
     * @property 
     * @method 
     */

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['verifgoogle', 'login']);
    }
    /**
     * verifgoogle permet de faire l'authentification en requete du front
     * vers google avec les éléments de conncetion
     * scope, credential,et code genere par google pour obtenir un token
     * stoker en bdd
     * @return token
     */
    public function verifgoogle()
    {
        $this->autoRender = false;
        $client = new Google\Client();
        $client->setAuthConfig('./config/code_secret_client.json');
        // ajout des scopes qui sont dans le front ici besoin mail 
        $client->addScope("https://mail.google.com/"); //ici tout pour les mails 
        $client->setRedirectUri('http://localhost:3000/login');

        if (isset($this->request->getData()['code'])) {
            $access_token = $client->fetchAccessTokenWithAuthCode($this->request->getData()['code']);
            $coderet = $this->request->getData()['code'];
            $oauth2 = new Google\Service\Oauth2($client);
            $userInfo = $oauth2->userinfo->get();
            // insert les donées en bdd
            $verif = $this->insertdata($userInfo['email'], $access_token['access_token'], $coderet);
            if ($verif) { // si utilisateur en base de donnée
                return $this->setJsonResponse($access_token['access_token']); //envoie du token
            } else {
                return $this->setJsonResponse("acces non autoriser"); // envoie erreur   
            }
        } else return $this->setJsonResponse('KO(non ok)');
    }
    /**
     * insertion en BDD des élèments selon l'email
     * code mis comme mot de passe pour le api-token 
     * appeler plus tard
     * 
     */

    public function insertdata($email, $token, $coderet)
    {
        $this->autoRender = false; // pour ne pas rendre une vue (template) cake
        $user = $this->Users->find()
            ->where(['email' => $email])
            ->first();


        if (!$user) { // si user n'existe pas
            return (false);
        } else {
            $user->password = $coderet; //met a jour le mot de passe
            $user->token = $token; //remplace le token
            $this->Users->save($user); //enregistre
            return (true);
        }
    }
    /**
     * permet de faire le lien entre le front et le back en creent un token api unique
     * stoké en BDD grace a l'email
     * 
     */
    public function login()
    {
        $unique = configure::read('unique');
        $this->autoRender = false; // pour ne pas rendre une vue (template) cake
        $result = $this->Authentication->getResult(); //verification du mail et password

        if ($result->isValid() && $unique == 0) { //si ok
            $user = $result->getData();
            $payload = [ //construction de l'entete
                'sub' => $user->id,
                'exp' => time() + 604800, //temps ou le token est actif 7 jours
            ];
            $jwt = JWT::encode($payload, Security::getSalt(), 'HS256'); //encodage du jwt
            $user->api_token = $jwt; //ajoute le jwt en base 
            $this->Users->save($user); //enregistre
            $json = [
                'token' => $jwt
            ];
            configure::write('unique', 1);
        } else {
            $this->response = $this->response->withStatus(401);
            $json =  [
                'result' => $result->getErrors(),

            ];
        }
        Log::debug(print_r("je suis loger", true));
        return $this->setJsonResponse($json);
    }
    /**
     * deconnection et suppression de la BDD du token google et du token api 
     * sans cela plus de connection possible
     * 
     */

    public function logout()
    {

        $this->autoRender = false; // pour ne pas rendre une vue (template) cake
        $user = $this->Authentication->getResult()->getData();
        $user->api_token = " ";
        $user->token = " ";
        $this->Users->save($user); //enregistre
        $json = [
            'reponse' => 'Vous etes déconnecter'
        ];
        Configure::write('unique', 0);
        Log::debug(print_r("je suis deloger", true));
        return $this->setJsonResponse($json);
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->autoRender = false;
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->autoRender = false;
        $user = $this->Users->get($id, [
            'contain' => ['role'],
        ]);

        $this->set(compact('user'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
