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
        //  $client->addScope("email");
        //   $client->addScope("profile");
        //  $client->addScope("https://www.googleapis.com/auth/drive");
        // ajout des scopes qui sont dans le front si besoin mail 
        $client->addScope("https://mail.google.com/"); //ici tout pour les mails
        
        $client->setRedirectUri('http://localhost:3000/login');
        if (isset($this->request->getData()['code'])) {
            $access_token = $client->fetchAccessTokenWithAuthCode($this->request->getData()['code']);
            $oauth2 = new Google\Service\Oauth2($client);
            $userInfo = $oauth2->userinfo->get();
            // insert les donées en bdd
            $this->insertdata($userInfo['email'], $userInfo['name'], $userInfo['id'], $access_token['access_token'],);
            return $this->setJsonResponse($access_token['access_token']);
        } else return $this->setJsonResponse('KO');
    }


    public function insertdata($email, $name, $idg, $token)
    {
        $this->autoRender = false; // pour ne pas rendre une vue (template) cake
        $user = $this->Usergoogles->find()
            ->where(['email' => $email])
            ->first();

        if (!$user) { // si user n'existe pas
            $user = $this->Usergoogles->newEntity([
                'name' => $name,
                'email' => $email,
                'tokenGoogle' => $token,
                'idgoogle' => $idg
            ]);
            if ($this->Usergoogles->save($user)) {
                $id = $user->id; // L'entity $user contient maintenant l'id

            }
        } else {
            $user->tokenGoogle = $token; //remplace le token
            $this->Usergoogles->save($user); //enregistre
        }
    }
    public function recupmail($mail)
    {
        $this->autoRender = false;
        $user = $this->Usergoogles->find()
            ->where(['email' => $mail])
            ->first();
        $token = $user['tokenGoogle'];
        $client = new Google\Client();

        $client->setAccessToken($token);
        $this->log(print_r($client, true), 'debug');
        $service = new Google\Service\Drive($client);
        $optParams = [];
        $optParams['maxResults'] = 5; //Return Only 5 Messages
        $optParams['labelIds'] = 'UNREAD'; //Only show messages unread
        $messages = $service->users_messages->listUsersMessages('me', $optParams);
        $list = $messages->getMessages();
        $messageId = $list[0]->getId(); //Grab first Message
        $optParamsGet = [];
        $optParamsGet['format'] = 'full'; //Display message in payload
        $message = $service->users_messages->get('me', $messageId, $optParamsGet);
        $messagePayload = $messages->getPayload();
        $headers = $message->getPayload()->getHeaders();
        $parts = $messages->getPayload()->getParts();
        $this->log(print_r($parts, true), 'debug');
        $body = $parts['body']; //['body'];
        /*   $rawData = $body->data;
        $sanitizedData = strtr($rawData, '-_', '+/');
        $decodedMessage = base64_decode($sanitizedData);
        $this->log(print_r($decodedMessage, true), 'debug');*/
        var_dump($body);
    }
}
