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
     * @var Bindable[]
     */
    private $listener = array();
    private $flushed = false;

    /**
     * @param Request $request
     * @param array $config
     */
    public function __construct(Request $request, array $config)
    {
        $this->request = $request;
        $this->config = $config;
        $this->flushed = false;
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

        if ($this->flushed) {
            throw new \RuntimeException('calling "set" after flushed');
        }
        if (is_object($key)) {
            $key = serialize($key);
        }
        if (is_scalar($key) && strpos($key, '.') === false) {
            $_SESSION[$key] = $value;
        } else {
            if (is_string($key)) {
                $key = explode('.', $key);
            }
            $_SESSION = $this->copyRecursive($_SESSION, $key, $value);
        }
        return $this;
    }


    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        if (!isset($key)) {
            throw new \InvalidArgumentException('$key is undefined');
        }
        $array = $_SESSION;
        if (isset($array[$key])) return $array[$key];
        $tmp = $array;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($tmp) || !isset($tmp[$segment])) {
                $tmp = null;
                break;
            } elseif (isset($tmp[$segment])) {
                $tmp = $tmp[$segment];
            } else {
                $tmp = null;
                break;
            }
        }
        return isset($tmp) ? $tmp : $default;
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
        foreach ($this->listener as $bindable) {
            $this->set($bindable->getSessionKey(), $bindable->getSessionContent());
        }
        session_write_close();
        $this->flushed = true;
    }


    /**
     * @param $array
     * @param $keys
     * @param $value
     * @return array
     */
    public function copyRecursive($array, $keys, $value)
    {
        is_array($array) || $array = [];
        $key = array_shift($keys);
        $array[$key] = $keys ? $this->copyRecursive(isset($array[$key]) ?: array(), $keys, $value) : $value;
        return $array;
    }

    /**
     * return flushed or not yet
     * @return bool true=flushed, false=not yet
     */
    public function isFlushed()
    {
        return $this->flushed;
    }

    /**
     * destructor.
     * if flushed session not yet, do it.
     */
    public function __destruct()
    {
        if (!$this->flushed) {
            $this->flush();
        }
    }

    /**
     * @param Bindable $obj
     */
    public function bind(Bindable $obj)
    {
        $obj->setSessionContent($this->get($obj->getSessionKey(), array()));
        $this->listener[] = $obj;
    }
}