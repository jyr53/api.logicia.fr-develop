<?php

declare(strict_types=1);

namespace App\Controller\Api\Tcia;

// require_once '/path/to/your-project/vendor/autoload.php';

use App\Controller\AppController;
use App\Controller\Traits\ResponseTrait;
use Cake\Event\EventInterface;
use Cake\Log\Log;
use CakeDC\Auth\Rbac\Rbac;
use GuzzleHttp\Psr7\ServerRequest;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use CakeDC\Auth\Social\Mapper\Google as MapperGoogle;
use Google;
use Google\Service;
use Google\Service\Gmail\Resource\Users;
use Google\Service\Oauth2;
use Cake\ORM\TableRegistry;
use PharIo\Manifest\Email;

require_once 'vendor/autoload.php';


class UsergooglesController extends AppController
{
    use ResponseTrait;


    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['verifgoogle', 'recupmail']);
    }

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
            $this->log(print_r($coderet, true), 'debug');
            $oauth2 = new Google\Service\Oauth2($client);
            $userInfo = $oauth2->userinfo->get();
            // insert les donées en bdd
            $verif = $this->insertdata($userInfo['email'], $access_token['access_token'], $coderet);
            $this->log(print_r($verif, true), 'debug');
            if ($verif) {
                return $this->setJsonResponse($access_token['access_token']);
            } else {
                return $this->setJsonResponse("acces non autoriser");
            }
        } else return $this->setJsonResponse('KO(non ok)');
    }


    public function insertdata($email, $token, $coderet)
    {
        $this->autoRender = false; // pour ne pas rendre une vue (template) cake
        $user = $this->User->find()
            ->where(['email' => $email])
            ->first();

        if (!$user) { // si user n'existe pas
            return ("false");
        } else {
            $user->password = $coderet; //met a jour le mot de passe
            $user->token = $token; //remplace le token
            $this->User->save($user); //enregistre
            return ("true");
        }
    }
    public function recupmail($mail)
    {
        $this->autoRender = false;
        $user = $this->Users->find()
            ->where(['email' => $mail])
            ->first();
        $token = $user['tokenGoogle'];
        $client = new Google\Client();
        $client->setAccessToken($token);
        $service = new Google\Service\Gmail($client);

        $optParams = [];
        $optParams['maxResults'] = 5; // Return Only 5 Messages
        $optParams['labelIds'] = 'INBOX'; // Only show messages in Inbox
        $messages = $service->users_messages->listUsersMessages('me', $optParams);
        $list = $messages->getMessages();
        $messageId = $list[0]->getId(); // Grab first Message


        $optParamsGet = [];
        $optParamsGet['format'] = 'full'; // Display message in payload
        $message = $service->users_messages->get('me', $messageId, $optParamsGet);
        $messagePayload = $message->getPayload();
        $headers = $message->getPayload()->getHeaders();
        $parts = $message->getPayload()->getParts();

        $body = $parts[0]['body'];
        $rawData = $body->data;
        $sanitizedData = strtr($rawData, '-_', '+/');
        $decodedMessage = base64_decode($sanitizedData);
        $this->log(print_r($decodedMessage, true), 'debug');
        return $decodedMessage;
        // return $this->setJsonResponse($decodedMessage);
    }
}
