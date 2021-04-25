<?php

namespace Feather\Session\Drivers;

/**
 * Description of Database
 *
 * @author fcarbah
 */
class DatabaseDriver extends Driver
{

    private $db;
    private $config;
    private $table = 'feather_session';

    /**
     *
     * @param array $config DB configuration options ['dsn'=>'', 'user' =>'', 'password'=>''
     * Associative array of PDO database config options 'dsn' ,'user', 'password' etc
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function activate()
    {
        $this->connect();
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
        $sql = 'delete from ' . $this->table . ' where id=:id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);

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

        $sql = 'delete from ' . $this->table . ' where access < :old';

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
        $sql = 'select * from ' . $this->table . ' where id=:id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $row ? $row['data'] : '';
        }

        return '';
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

        $time = time();

        $sql = 'replace into ' . $this->table . ' (id,data,access) values(:id,:data,:access)';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':data', $data);
        $stmt->bindValue(':access', $time);

        return $stmt->execute();
    }

    /**
     *
     * @throws \Exception
     */
    protected function connect()
    {
        if (!$this->db) {
            try {
                $this->db = new \PDO($this->config['dsn'], $this->config['user'], $this->config['password']);
            } catch (\Exception $e) {
                throw new \Exception('Error connecting to database', 300, $e);
            }
        }
    }

}
