<?php
namespace Hoimi;

use Hoimi\Exception\NotFoundException;

/**
 * Class Router
 *
 * this is default router
 * @package Hoimi
 */
class Router extends BaseRouter
{
    private $staticRoutes = array();
    private $dynamicRoutes = array();

    /**
     * @param Request $request
     * @return mixed
     * @throws Exception\NotFoundException
     */
    public function resolveClassName(Request $request)
    {
        $path = $this->trimPath($request->parseUrl()['path']);
        if (isset($this->staticRoutes[$path])) {
            return $this->staticRoutes[$path];
        }
        foreach ($this->dynamicRoutes as $key => $value) {
            if (!preg_match_all('/(\{(.+?)\})/uS', $key, $names)) continue;
            $key = str_replace('#', '\#', str_replace($names[1], '(.+?)', $key));
            if (!preg_match("#^$key$#u", $path, $values)) continue;
            $result = array();
            for ($i = 0, $length = count($names[2]); $i < $length; $i++) {
                $result[$names[2][$i]] = $values[$i + 1];
            }
            $request->setUrlParameters($result);
            return $value;
        }
        throw new NotFoundException('path not found' . $path);
    }

    public function trimPath($path)
    {
        return trim($path);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function resolveMethodName(Request $request)
    {
        return strtolower($request->getHeader('REQUEST_METHOD'));
    }

    public function setRoutes(array $routes, $safe = true)
    {
        parent::setRoutes($routes, $safe);
        $routes = $this->getRoutes();
        foreach ($routes as $key => $value) {
            if (strpos($key, '{') === false) {
                $this->staticRoutes[$key]  = $value;
            } else {
                $this->dynamicRoutes[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * @return null
     */
    public function getDynamicRoutes()
    {
        return $this->dynamicRoutes;
    }

    /**
     * @return null
     */
    public function getStaticRoutes()
    {
        return $this->staticRoutes;
    }

    /**
     * @param array $dynamicRoutes
     * @return $this
     */
    public function setDynamicRoutes($dynamicRoutes)
    {
        $this->dynamicRoutes = $dynamicRoutes;
        return $this;
    }

    /**
     * @param array $staticRoutes
     * @return $this
     */
    public function setStaticRoutes($staticRoutes)
    {
        $this->staticRoutes = $staticRoutes;
        return $this;
    }
}

