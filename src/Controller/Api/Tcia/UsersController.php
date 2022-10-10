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


class UsersController extends AppController
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

        $this->Authentication->allowUnauthenticated(['verifgoogle', 'getuser', 'recupmail']);
    }
    public function getuser()
    {
        $this->autoRender = false;
        header('Location: https://www.googleapis.com/oauth2/v3/userinfo');
        exit();
    }
    public function verifgoogle()
    {
        $this->autoRender = false;
        $client = new Google\Client();
        $client->setAuthConfig('./config/code_secret_client.json');
        $client->addScope("email");
        $client->addScope("profile");
        $client->addScope("https://mail.google.com/");
        $client->addScope("https://www.googleapis.com/auth/drive");
        $client->setRedirectUri('http://localhost:3000/login');
        if (isset($this->request->getData()['code'])) {
            $access_token = $client->fetchAccessTokenWithAuthCode($this->request->getData()['code']);
            $oauth2 = new Google\Service\Oauth2($client);
            $userInfo = $oauth2->userinfo->get();

            //  $this->log(print_r($userInfo, true), 'debug');
            Configure::write('googleuser', $userInfo);
            // insert les donées
            $this->insertdata($userInfo['email'], $userInfo['name'], $access_token['access_token']);
            return $this->setJsonResponse($access_token['access_token']);
        } else return $this->setJsonResponse('KO');
    }

    public function insertdata($email, $name, $token)
    {
        $this->autoRender = false; // pour ne pas rendre une vue (template) cake
        $user = $this->Users->find()
            ->where(['email' => $email])
            ->first();

        if (!$user) { // si user n'existe pas
            $user = $this->Users->newEntity([
                'name' => $name,
                'email' => $email,
                'tokenGoogle' => $token
            ]);
            if ($this->Users->save($user)) {
                $id = $user->id; // L'entity $user contient maintenant l'id
            }
        } else {
            $user->tokenGoogle = $token; //remplace le token
            $this->Users->save($user); //enregistre
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
        $client->setAuthConfig('./config/code_secret_client.json');
        $client->setRedirectUri('http://localhost:3000/login');
        $client->addScope('email');
        $client->addScope('profile');
        $client->addScope('https://mail.google.com');
        $client->setAccessToken($token);
        $client->setAccessType('offline');


        $service = new Google\Service\Gmail($client);
        $messages = $service->users_messages->listUsersMessages('me',);
        $list = $messages->getMessages();
        $messageId = $list[0]->getId(); //Grab first Message
        $optParamsGet = [];
        $optParamsGet['format'] = 'full'; //Display message in payload
        $message = $service->users_messages->get('me', $messageId, $optParamsGet);
        $messagePayload = $message->getPayload();
        $headers = $message->getPayload()->getHeaders();
        $parts = $message->getPayload()->getParts();

        $body = $parts[0]['body'];
        $rawData = $body->data;
        $sanitizedData = strtr($rawData, '-_', '+/');
        $decodedMessage = base64_decode($sanitizedData);

        var_dump($decodedMessage);
    }
}
