<?php
declare(strict_types=1);

namespace App\Controller\Api\Tcia;
use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Utility\Security;
use Firebase\JWT\JWT;
use App\Controller\Traits\ResponseTrait;

/**
 * Users Controller
 *
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 */
class AuthsController extends AppController
{

    use ResponseTrait;

    public function initialize(): void {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated(['login']);
    }

    public function login()
    {
        
        $result = $this->Authentication->getResult();
        if ($result->isValid()) {
            $user = $result->getData();
            $payload = [
                'sub' => $user->id,
                'exp' => time() + 604800,
            ];

            $json = [
                'token' => JWT::encode($payload, Security::getSalt(), 'HS256'),
                'user' => $user
            ];
        } else {
            $this->response = $this->response->withStatus(401);
            $json =  [
                'result' => $result->getErrors(),
            ];
        }
        
        return $this->setJsonResponse($json);
    }

    public function index()
    {
		$this->log("index", 'debug');
        $identity = $this->Authentication->getIdentity();
		$this->log(print_r($identity->getOriginalData(),true), 'debug');

        $json = ['user' => $identity->getOriginalData()];

        return $this->setJsonResponse($json);
    }

    public function logout()
    {
        $json = [];

        $this->Authentication->logout();

        $this->set(compact('json'));
        $this->viewBuilder()->setOption('serialize', 'json');
    }
}
