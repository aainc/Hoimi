<?php
namespace Hoimi;
use Hoimi\Session\DatabaseDriver;

/**
 * Class Session
 * @package Hoimi
 */
class Session
{
    private $request = null;
    private $config = null;
    private $driver = null;

    /**
     * @param Request $request
     * @param array $config
     */
    public function __construct(Request $request, array $config)
    {
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * session start
     * @throws \RuntimeException
     */
    public function start()
    {
        $sessionId = null;
        if (isset($this->config['keyName'])) {
            $sessionId = $this->request->get($this->config['keyName']);
            if ($sessionId !== '') {
                session_id($sessionId);
            }
        }
        if (isset($this->config['maxLifeTime'])) {
            session_set_cookie_params($this->config['maxLifeTime']);
        }
        if (isset($this->config['driver'])) {
            $store = $this->config['driver'];
            if ($store === 'driver') {
                $this->driver = new DatabaseDriver($this->config['database']);
            } else {
                throw new \RuntimeException('Un supported session:' . $store);
            }
            session_set_save_handler($this->driver, true);
        }
        session_start();
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value)
    {
        return $_SESSION[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $_SESSION[$key];
    }

    /**
     * @param $key
     */
    public function delete($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return session_id();
    }

    public function setId($id)
    {
        return session_id($id);
    }

    /**
     * regenerate session id
     */
    public function regenerateId()
    {
        session_regenerate_id(true);
    }

    /**
     * session write and close
     */
    public function flush()
    {
        session_write_close();
    }
}