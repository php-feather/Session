<?php

namespace Feather\Session\Drivers;

use Feather\Support\Database\Connection;

/**
 * Description of Database
 *
 * @author fcarbah
 */
class DatabaseDriver extends Driver
{

    /** @var \Feather\Support\Database\Connection * */
    private $db;

    /** @var array * */
    private $config;

    /** @var string * */
    private $table = 'sessions';

    /**
     *
     * @param array $config DB configuration options ['table' => '']
     * Associative array of PDO database config options 'dsn' ,'user', 'password' etc
     */
    public function __construct(\Feather\Support\Database\Connection $db, array $config)
    {
        $this->table  = $config['table'] ?? $this->table;
        $this->config = $config;
        $this->db     = $db;
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
     * @return bool
     */
    public function close()
    {
        return $this->db->close();
    }

    /**
     *
     * @param string|int $id
     * @return type
     */
    public function destroy($id)
    {
        $sql  = 'DELETE FROM ' . $this->table . ' where id = :id';
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
        $stmt->bindValue(':old', $old, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     *
     * @return bool
     */
    public function open()
    {
        return $this->db->connect();
    }

    /**
     *
     * @param string|int $id
     * @return string
     */
    public function read($id)
    {
        $expTime = time() - $this->getTimeout();

        $sql = 'SELECT id, sess_data, expire_at FROM ' . $this->table .
                ' WHERE id = :id AND expire_at > :time';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_STR);
        $stmt->bindValue(':time', $expTime, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $row ? unserialize($row['sess_data']) : '';
        }

        return '';
    }

    /**
     * Set Database connection
     * @param \Feather\Support\Database\Connection $db
     * @return $this
     */
    public function setDBConnection(\Feather\Support\Database\Connection $db)
    {
        $this->db = $db;
        return $this;
    }

    /**
     *
     * @param string|int $id
     * @param mixed $data
     * @return boolean
     */
    public function write($id, $data)
    {
        $time = time() + $this->getTimeout();

        $sql = 'REPLACE INTO ' . $this->table . ' (id, sess_data, expire_at) values(:id, :sess_data, :expire_at)';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_STR);
        $stmt->bindValue(':sess_data', serialize($data), \PDO::PARAM_STR);
        $stmt->bindValue(':expire_at', $time, \PDO::PARAM_INT);

        return $stmt->execute();
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
