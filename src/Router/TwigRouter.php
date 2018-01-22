<?php
declare(strict_types=1);

namespace App\Router;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Silex\Application;
use Symfony\Component\DependencyInjection\Container;
use Twig_Environment;
use App\Exception\NotFoundException;
use App\Exception\AccessDeniedException;
use App\Exception\MethodNotAllowedException;

/**
 * @see https://github.com/symlex/symlex#routing-and-rendering
 */
class TwigRouter extends Router
{
    use SessionTrait;

    protected $twig;
    protected $realm;

    public function __construct(Application $app, Container $container, Twig_Environment $twig)
    {
        parent::__construct($app, $container);
        $this->twig = $twig;
    }

    public function route($routePrefix = '', $servicePrefix = 'controller.web.', $servicePostfix = '')
    {
        $this->realm = strtolower(explode('.', $servicePrefix)[1]); // web or sharetoall
        $app = $this->app;
        $container = $this->container;

        $webRequestHandler = function ($controller, Request $request, $action = '') use ($container, $servicePrefix, $servicePostfix) {
            // indexAction is default
            if (!$action) {
                $action = 'index';
            }

            // Remove trailing .html
            if (stripos($action, '.html') === (strlen($action) - 5)) {
                $action = substr($action, 0, -5);
            }

            $prefix = strtolower($request->getMethod());
            $parts = explode('/', $action);

            $subResources = '';
            $params = array();

            $count = count($parts);

            for ($i = 0; $i < $count; $i++) {
                $subResources .= ucfirst($parts[$i]);

                if (isset($parts[$i + 1])) {
                    $i++;
                    $params[] = $parts[$i];
                }
            }

            $params[] = $request;
            $actionName = $prefix . $subResources . 'Action';

            $controllerService = $servicePrefix . strtolower($controller) . $servicePostfix;

            $controllerInstance = $this->getController($controllerService);

            if ($prefix == 'get' && !method_exists($controllerInstance, $actionName)) {
                $actionName = $subResources . 'Action';
            }

            if (!method_exists($controllerInstance, $actionName)) {
                if (method_exists($controllerInstance, $subResources . 'Action')) {
                    throw new MethodNotAllowedException ($request->getMethod() . ' not supported');
                } else {
                    throw new NotFoundException ($actionName . ' not found');
                }
            }

            if (!$this->hasPermission($request)) {
                throw new AccessDeniedException ('Access denied');
            }

            $result = call_user_func_array(array($controllerInstance, $actionName), $params);

            $this->setTwigVariables($controller, $subResources, $request->isXmlHttpRequest());

            $template = $this->getTemplateFilename($controller, $subResources);

            $response = $this->getResponse($result, $template);

            return $response;
        };

        $indexRequestHandler = function (Request $request) use ($app, $container, $servicePrefix, $servicePostfix, $webRequestHandler) {
            return $webRequestHandler('index', $request, 'index');
        };

        $app->get($routePrefix . '/', $indexRequestHandler);
        $app->match($routePrefix . '/{controller}', $webRequestHandler);
        $app->match($routePrefix . '/{controller}/', $webRequestHandler);
        $app->match($routePrefix . '/{controller}/{action}', $webRequestHandler)->assert('action', '.+');
    }

    protected function render(string $template, array $values, int $httpCode = 200): Response
    {
        $result = $this->twig->render(strtolower($template), $values);

        return new Response($result, $httpCode);
    }

    protected function redirect(string $url, int $httpCode = 302): Response
    {
        $result = new RedirectResponse($url, $httpCode);

        return $result;
    }

    protected function setTwigVariables(string $controller, string $action, bool $isXmlHttpRequest)
    {
        $session = $this->getSession();

        $this->twig->addGlobal('realm', $this->realm);
        $this->twig->addGlobal('controller', strtolower($controller));
        $this->twig->addGlobal('action', strtolower($action));
        $this->twig->addGlobal('ajax_request', $isXmlHttpRequest);
        $this->twig->addGlobal('user_id', $session->hasUserId() ? $session->getUserId() : '');
        $this->twig->addGlobal('session_token', $session->hasToken() ? $session->getToken() : '');
        $this->twig->addGlobal('is_anonymous', $this->session->isAnonymous());
    }

    protected function getTemplateFilename(string $controller, string $subResources): string
    {
        $result = $this->realm . '/' . $controller . '/' . $subResources . '.twig';

        return $result;
    }

    protected function getResponse($result, string $template): Response
    {
        if (is_object($result) && $result instanceof Response) {
            $response = $result;
        } elseif (is_string($result) && $result != '') {
            $response = $this->redirect($result);
        } else {
            $response = $this->render($template, (array)$result);
        }

        return $response;
    }
}
