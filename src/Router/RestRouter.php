<?php
declare(strict_types=1);

namespace App\Router;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ActiveRecord\Search\SearchResult;
use App\Exception\MethodNotAllowedException;
use App\Exception\AccessDeniedException;

/**
 * @see https://github.com/symlex/symlex#routing-and-rendering
 */
class RestRouter extends Router
{
    use SessionTrait;

    public function route($routePrefix = '/api', $servicePrefix = 'controller.rest.', $servicePostfix = '')
    {
        $app = $this->app;
        $container = $this->container;

        $handler = function ($path, Request $request) use ($container, $servicePrefix, $servicePostfix) {
            $contentType = $request->headers->get('Content-Type');
            if ($contentType == null || 0 === strpos($contentType, 'application/json')) {
                $data = json_decode($request->getContent(), true);
                $request->request->replace(is_array($data) ? $data : array());
            }

            $method = $request->getMethod();

            $prefix = strtolower($method);
            $parts = explode('/', $path);

            $controller = array_shift($parts);

            $subResources = '';
            $params = array();

            $count = count($parts);

            for ($i = 0; $i < $count; $i++) {
                $params[] = $parts[$i];
            }

            $params[] = $request;
            $actionName = $prefix . $subResources . 'Action';

            $controllerService = $servicePrefix . strtolower($controller) . $servicePostfix;

            $controllerInstance = $this->getController($controllerService);

            if (!method_exists($controllerInstance, $actionName)) {
                throw new MethodNotAllowedException ('Method ' . $method . ' not supported with action '. $actionName);
            }

            if (!$this->hasPermission($request)) {
                throw new AccessDeniedException ('Access denied');
            }

            $result = call_user_func_array(array($controllerInstance, $actionName), $params);

            if (!$result) {
                $httpCode = 204;
            } elseif ($method == 'POST') {
                $httpCode = 201;
            } else {
                $httpCode = 200;
            }

            $response = $this->getResponse($result, $httpCode);

            return $response;
        };

        $app->match($routePrefix . '/{path}', $handler)->assert('path', '.+');
    }

    protected function getResponse($result, int $httpCode): Response
    {
        $headers = array();

        if (is_object($result)) {
            if ($result instanceof Response) {
                // If controller returns Response object, return it directly
                return $result;
            } elseif ($result instanceof SearchResult) {
                // Add special headers to search results
                $headers['X-Result-Total'] = $result->getTotalCount();
                $headers['X-Result-Order'] = $result->getSortOrder();
                $headers['X-Result-Count'] = $result->getSearchCount();
                $headers['X-Result-Offset'] = $result->getSearchOffset();

                $result = $result->getAllResultsAsArray();
            }
        }

        return $this->app->json($result, $httpCode, $headers);
    }
}
