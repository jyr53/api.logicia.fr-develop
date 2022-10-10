<?php

namespace App\Controller\Traits;

use Cake\Log\Log;

/**
 * Response trait
 */
trait ResponseTrait {

    /**
     * Json return type
     *
     * @var string
     */
    private $jsonType = 'application/json';

    /**
     * Prepare and return json response
     *
     * @param  mixed $data Data to return as a json response
     * @param  int $status HTTP status code
     * @return \Cake\Http\Response
     */
    public function setJsonResponse($data, $status = null) {
        $data = $this->prepareData($data);

        return $this->prepareResponse($status)->withStringBody($data);
    }

    /**
     * Prepare data to send response as a json response
     *
     * @param  mixed $data Data to prepare
     * @return string
     */
    private function prepareData($data) {
        if (is_array($data)) {
            $data = json_encode($data);
        } elseif (is_object($data)) {
            $data = json_encode($data);
        }

        return $data;
    }

    /**
     * Returns proper response object with status condition
     *
     * @param  int|null $status HTTP status code
     * @return \Cake\Http\Response
     */
    private function prepareResponse($status) {
        $response = $this->getResponse()->withType($this->jsonType);

        if ($status !== null) {
            $response = $response->withStatus($status)
                    ->withHeader('Access-Control-Allow-Methods', 'POST, GET, PUT, PATCH, DELETE, OPTIONS')
                    ->withHeader('Access-Control-Allow-Credentials', 'true')
                    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With')
                    ->withHeader('Access-Control-Allow-Headers', 'Content-Type')
                    ->withHeader('Access-Control-Allow-Type', 'application/json');
        }

        return $response->cors($this->request)
                        ->allowOrigin(['*'])
                        ->allowMethods(['GET', 'POST','POST','GET','PUT','PATCH','DELETE','OPTIONS'])
                        ->allowHeaders(['X-CSRF-Token', '*'])
                        ->allowCredentials()
                        ->exposeHeaders(['Link'])
                        ->maxAge(300)
                        ->build();
    }

}
