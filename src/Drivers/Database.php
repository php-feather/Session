<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Feather\Session\Drivers;

/**
 * Description of Database
 *
 * @author fcarbah
 */
class Database {
    
    private $db;
    private $config;
    private $table = 'feather_session';
    
    public function __construct($config) {
        $this->config = $config; 
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
    
    public function close(){
        if($this->db){
            $this->db = null;
            return true;
        }
        return false;
    }
    
    public function destroy($id){
        $this->connect();
        $sql = 'delete from '.$this->table.' where id=:id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id',$id);
        
        return $stmt->execute();
    }
    
    public function gc($max){
        
        $old = time() - $max;
        
        $sql = 'delete from '.$this->table.' where access < :old';
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':old',$old);
        
        return $stmt->execute();
    }
    
    public function open(){
        
        $this->connect();
        
        return $this->db? true : false;
    }
    
    public function read($id){
        $this->connect();
        $sql = 'select * from '.$this->table.' where id=:id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id',$id);
        
        if($stmt->execute()){
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return $row? $row['data'] : '';
        }
        
        return '';
    }
    
    public function write($id,$data){
        
        $this->connect();
        
        $time = time();
        
        $sql = 'replace into '.$this->table.' (id,data,access) values(:id,:data,:access)';
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id',$id);
        $stmt->bindValue(':data',$data);
        $stmt->bindValue(':access',$time);
        
        return $stmt->execute();
    }
    
    protected function connect(){
        if(!$this->db){
            try{
                $this->db = new \PDO($this->config['dsn'], $this->config['user'], $this->config['password']);
            }
            catch (\Exception $e){
                throw new \Exception('Error connecting to database',300,$e);
            }
        }
    }
    
}
