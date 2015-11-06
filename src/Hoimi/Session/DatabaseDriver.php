<?php
namespace Hoimi\Session;
use Hoimi\Config;

/**
 * Class DatabaseDriver
 * @package Hoimi\Session
 */
class DatabaseDriver implements \SessionHandlerInterface
{
    private $config = null;
    /**
     * @var \mysqli
     */
    private $connection = null;

    /**
     * @param Config $config
     */
    function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return bool|void
     */
    public function close()
    {
        $this->connection->close();
    }

    /**
     * @param int $session_id
     * @return bool|void
     */
    public function destroy($session_id)
    {
        $stmt = $this->connection->prepare("DELETE FROM session_store WHERE session_id=?");
        $stmt->bind_param("s", $session_id);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param int $maxlifetime
     * @return bool|void
     */
    public function gc($maxlifetime)
    {
        $stmt = $this->connection->prepare("DELETE FROM session_store WHERE updated_at < ?");
        $x = (time() - $maxlifetime);
        $stmt->bind_param("d", $x);
        $stmt->execute();
        $stmt->close();

    }

    public function open($save_path, $session_id)
    {
        if ($this->connection === null) {
            $this->connection = new \mysqli(
                $this->config['host'],
                $this->config['user'],
                $this->config['pass']
            );
            $this->connection->select_db($this->config['database']);
        }
    }

    /**
     * @param string $session_id
     * @return null|string
     */
    public function read($session_id)
    {
        $stmt = $this->connection->prepare("SELECT session_data FROM session_store WHERE session_id=?");
        $stmt->bind_param("s", $session_id);
        $stmt->execute();
        $data = null;
        $stmt->bind_result($data);
        $data = $stmt->fetch() ? $data : '';
        $stmt->close();
        return $data;
    }

    /**
     * @param string $session_id
     * @param string $session_data
     * @return bool|void
     */
    public function write($session_id, $session_data)
    {
        $now = time();
        if ($this->read($session_id) === '') {
            $stmt = $this->connection->prepare("INSERT INTO session_store(session_id, session_data, updated_at) VALUES (?, ?, ?)");
            $stmt->bind_param("ssd", $session_id, $session_data, $now);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $this->connection->prepare("UPDATE session_store SET session_data=?, updated_at=? WHERE session_id=?");
            $stmt->bind_param("sds", $session_data, $now, $session_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * @return null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param null $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return null
     */
    public function getConnection()
    {
        return $this->connection;
    }
}