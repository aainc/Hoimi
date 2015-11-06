<?php
namespace Hoimi;
use Hoimi\Exception\NotFoundException;

/**
 * Class BaseRouter
 * @package Hoimi
 */
abstract class BaseRouter
{
    private $routes = array();

    /**
     * singleton
     * @return BaseRouter
     */
    public static function getInstance ()
    {
        static $instance;
        if ($instance === null) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * @param null $routes
     */
    public function __construct($routes = null)
    {
        if ($routes) {
            $this->setRoutes($routes);
        }
    }

    /**
     * routing(className and methodName) by Hoimi\Request
     *
     * this is template method
     * subclass need to implement "resolveClassName" and "resolveMethodName"
     * @param Request $request
     * @return array(instance of Action, methodName)
     * @throws \RuntimeException
     * @throws Exception\NotFoundException
     */
    public function run(Request $request)
    {
        $url = $request->parseUrl();
        if (!isset($url['path']) || !$url['path']) {
            $url['path'] = '/';
        }
        $className = $this->resolveClassName($request);
        if ($className === null || !class_exists($className)) {
            throw new NotFoundException('Unsupported class:' . $className);
        }

        $clazz = new \ReflectionClass($className);
        if (!$clazz->isSubClassOf('Hoimi\BaseAction')) {
            throw new \RuntimeException('InvalidClass:' . $className);
        }

        $requestMethod = $this->resolveMethodName($request);
        if (!$clazz->hasMethod($requestMethod)) {
            throw new NotFoundException('unsupported method :' . $requestMethod . '@' . $url['path']);
        }

        $action = $clazz->newInstance();
        return array($action, $requestMethod);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    abstract public function resolveClassName(Request $request);

    /**
     * @param Request $request
     * @return mixed
     */
    abstract public function resolveMethodName(Request $request);

    /**
     * @param array $routes
     * @param bool $safe
     * @return $this
     */
    public function setRoutes (array $routes, $safe = true)
    {
        if ($safe) {
            $keys = array_keys($routes);
            usort($keys, function ($a, $b) {return strlen($b) - strlen($a);});
            $temp = array();
            foreach ($keys as $key) $temp[$key] = $routes[$key];
            $routes = $temp;
        }
        $this->routes = $routes;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}