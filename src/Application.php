<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.3.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App;

use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Identifier\IdentifierInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use Cake\Core\Configure;
use Cake\Core\Exception\MissingPluginException;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Http\Session;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication implements AuthenticationServiceProviderInterface
{

    /**
     * Load all the application configuration and bootstrap logic.
     *
     * @return void
     */
    public function bootstrap(): void
    {
        // Call parent to load bootstrap from files.
        parent::bootstrap();

        if (PHP_SAPI === 'cli') {
            $this->bootstrapCli();
        }

        /*
         * Only try to load DebugKit in development mode
         * Debug Kit should not be installed on a production system
         */
        if (Configure::read('debug')) {
            $this->addPlugin('DebugKit');
        }

        $this->addPlugin('Authentication');
    }

    /**
     * Setup the middleware queue your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware queue.
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(Configure::read('Error')))

            // Handle plugin/theme assets like CakePHP normally does.
            ->add(new AssetMiddleware([
                'cacheTime' => Configure::read('Asset.cacheTime'),
            ]))

            // Add routing middleware.
            // If you have a large number of routes connected, turning on routes
            // caching in production could improve performance. For that when
            // creating the middleware instance specify the cache config name by
            // using it's second constructor argument:
            // `new RoutingMiddleware($this, '_cake_routes_')`
            ->add(new RoutingMiddleware($this))

            // Parse various types of encoded request bodies so that they are
            // available as array through $request->getData()
            // https://book.cakephp.org/4/en/controllers/middleware.html#body-parser-middleware
            ->add(new BodyParserMiddleware())
            ->add(new AuthenticationMiddleware($this));
        $csrf = new CsrfProtectionMiddleware([
            'httponly' => true,
        ]);

        // Token check will be skipped when callback returns `true`.
        $csrf->skipCheckCallback(function ($request) {
            //            $this->log("API application=>" . $this->request->getParam('action'), 'debug');
            // Skip token check for API URLs.
            //if ($request->getParam('prefix') === 'api') {
            return true;
            //}
        });
        //        $this->log("API application=>" . $this->request->getParam('action'), 'debug');
        // Ensure routing middleware is added to the queue before CSRF protection middleware.
        $middlewareQueue->add($csrf);
        return $middlewareQueue;
    }

    /**
     * Bootstrapping for CLI application.
     *
     * That is when running commands.
     *
     * @return void
     */
    protected function bootstrapCli(): void
    {
        try {
            $this->addPlugin('Bake');
        } catch (MissingPluginException $e) {
            // Do not halt if the plugin is missing
        }

        $this->addPlugin('Migrations');

        // Load more plugins here
    }

    /**
     * Returns a service provider instance.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request
     * @return \Authentication\AuthenticationServiceInterface
     */
    public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
    {
        $service = new AuthenticationService();

        $fields = [

            //  'password' => '4/0ARtbsJrsHxPVC-IaZDioRvSm2bYZVaJvG74TiQu8eI3LzEUtm-odrg1lNSKbX59SMhZpHg'
            IdentifierInterface::CREDENTIAL_USERNAME => 'username',
            IdentifierInterface::CREDENTIAL_PASSWORD => 'password'
        ];
        $service->loadAuthenticator('Authentication.Session');
        $service->loadAuthenticator('Authentication.Jwt', [
            'secretKey' => Security::getSalt(),
            'queryParam' => 'token',
            'header' => 'Authorization',
            'tokenPrefix' => 'Bearer', // postman sends a tokenPrefix Bearer with the first capital letter
            'returnPayload' => false,
        ]);
        $service->loadAuthenticator('Authentication.Form', [
            'fields' => $fields,
        ]);

        // Load identifiers
        $service->loadIdentifier('Authentication.JwtSubject');
        $service->loadIdentifier('Authentication.Password', [
            'returnPayload' => false,
            'fields' => $fields,
        ]);

        return $service;
    }
}
