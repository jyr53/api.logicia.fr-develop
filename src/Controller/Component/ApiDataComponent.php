<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;
use Cake\Log\Log;
use CakeDC\Auth\Rbac\Rbac;
use ReflectionMethod;
use ReflectionClass;

class ApiDataComponent extends Component {

    // Exécute une autre configuration additionnelle pour votre component.
    public function initialize(array $config): void {

    }

    /**
     *
     * @description Récupération des noms des associés
     *
     * @param type $associes_ids
     * @return array $names
     * @returnformat Nom Prénom
     */
    public function getCrudActions($request, $entity) {

        // Obtenir l'identity à partir de la requête
        $user = $request->getAttribute('identity');
        //$identity = $request->getAttribute('authentication')->getIdentity();
        // Vérifier l'autorisation sur $article



        $rbac = new Rbac();
        //Log::debug(print_r($rbac, true));
        $isAuthorized = $rbac->checkPermissions($user, $request);
        //Log::debug(print_r($isAuthorized, true));
//        if ($user->can('delete', $entity)) {
//            Log::debug("ici");
//        }
//        $cruds[] = [
//        name: "Ajouter",
//        icon: "mdi-plus",
//        fname: "add"
//        ],
//        [
//        name: "Visualiser",
//        icon: "mdi-eye",
//        fname: "view"
//        ],
//        [
//        name: "Modifier",
//        icon: "mdi-pencil-box-outline",
//        fname: "edit"
//        ],
//        [
//        name: "Supprimer",
//        icon: "mdi-trash-can-outline",
//        fname: "delete"
//        ]
//
//
//        return $names;
    }

    public function getActions($controllerName, $prefixe = '') {
        $prefixe = $prefixe == '' ? $prefixe : ((str_replace("/", "\\", $prefixe)) . "\\" );
        $className = 'App\\Controller\\' . $prefixe . $controllerName . 'Controller';
        $class = new ReflectionClass($className);
        $actions = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        $controllerName = str_replace("\\", "/", $controllerName);
        $results = [$controllerName => []];
        $ignoreList = ['beforeFilter', 'afterFilter', 'initialize', 'beforeRender', 'setJsonResponse'];
        foreach ($actions as $action) {
            if ($action->class == $className && !in_array($action->name, $ignoreList)
            ) {
                array_push($results[$controllerName], $action->name);
            }
        }
        return $results;
    }

}
