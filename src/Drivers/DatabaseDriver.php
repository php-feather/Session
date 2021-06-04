<?php

namespace Feather\Session\Drivers;

use Feather\Support\Database\Dbal;

/**
 * Description of Database
 *
 * @author fcarbah
 */
class DatabaseDriver extends Driver
{

    /** @var \Feather\Support\Database\Dbal * */
    private $db;

    /** @var array * */
    private $config;

    /** @var string * */
    private $table = 'sessions';

    /**
     *
     * @param array $config DB configuration options ['dsn'=>'', 'user' =>'', 'password'=>'', 'pdoOptions' => [], 'table' => '']
     * Associative array of PDO database config options 'dsn' ,'user', 'password' etc
     */
    public function __construct(array $config)
    {
        $this->table = $config['table'] ?? $this->table;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function activate()
    {
        session_cache_limiter('private');
        session_set_save_handler(
                array($this, "open"),
                array($this, "close"),
                array($this, "read"),
                array($this, "write"),
                array($this, "destroy"),
                array($this, "gc")
        );
    }

    /**
     *
     * @return boolean
     */
    public function close()
    {
        if ($this->db) {
            $this->db = null;
            return true;
        }
        return false;
    }

    /**
     *
     * @param string|int $id
     * @return type
     */
    public function destroy($id)
    {
        $this->connect();
        $sql = 'DELETE FROM ' . $this->table . ' where id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     *
     * @param int $max
     * @return type
     */
    public function gc($max)
    {

        $old = time() - $max;

        $sql = 'DELETE FROM ' . $this->table . ' WHERE expire_at < :old';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':old', $old);

        return $stmt->execute();
    }

    /**
     *
     * @return boolean
     */
    public function open()
    {

        $this->connect();

        return $this->db ? true : false;
    }

    /**
     *
     * @param string|int $id
     * @return string
     */
    public function read($id)
    {
        $this->connect();
        $sql = 'SELECT id, sess_data, expire_at FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $row ? unserialize($row['sess_data']) : null;
        }

        return null;
    }

    /**
     *
     * @param string|int $id
     * @param mixed $data
     * @return boolean
     */
    public function write($id, $data)
    {

        $this->connect();

        $time = time() + $this->getTimeout();

        $sql = 'REPLACE INTO ' . $this->table . ' (id, sess_data, expire_at) values(:id, :sess_data, :expire_at)';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':sess_data', serialize($data));
        $stmt->bindValue(':expire_at', $time);

        return $stmt->execute();
    }

    /**
     *
     * @throws \Exception
     */
    protected function connect()
    {
        if (!$this->db) {
            $this->db = new Dbal($this->config['dsn'], $this->config['user'], $this->config['password'], $this->config['pdoOptions'] ?? []);
            $this->db->connect();
        }
    }

    /**
     *
     * @return int
     */
    protected function getTimeout()
    {
        return (int) ini_get('session.gc_maxlifetime');
    }

}
