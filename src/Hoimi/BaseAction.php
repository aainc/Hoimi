<?php
namespace Hoimi;
/**
 * Class BaseAction
 *
 * ActionClass need to extends this Abstract Class
 *
 * @package Hoimi
 */
abstract class BaseAction
{
    private $request = null;
    private $config = null;
    private $session = null;

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return Config|null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * if this action need SessionVariables, this method return true.
     * default false.
     * @return bool
     */
    public function useSessionVariables() {
        return false;
    }

    /**
     * @param null $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @return null
     */
    public function getSession()
    {
        return $this->session;
    }
}
