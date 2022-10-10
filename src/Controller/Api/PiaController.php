<?php
// src/Controller/Api/PiaController.php

namespace App\Controller\Api;

use App\Controller\AppController;

class PiaController extends AppController
{
    // public function index()
    // {
    //     $this->loadComponent('Paginator');
    //     $articles = $this->Paginator->paginate();
    //     $this->set(compact('articles'));
    // }

    // public function view($slug = null)
    // {
    //     $article = $this->Articles->findBySlug($slug)->firstOrFail();
    //     $this->set(compact('article'));
    // }

    public function index()
    {
        // var_dump('bonsoir');
        $this->autoRender = false;
        $this->viewBuilder()->setLayout("ajax");
        return $this->response->withType('application/json')
            ->withStringBody(json_encode('FooBar'));
    }
}