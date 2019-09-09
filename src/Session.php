<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Feather\Session;

/**
 * Description of Session
 *
 * @author fcarbah
 */
class Session {
    public static function flush($destroy=false){
        if($destroy){
            session_destroy();
        }
        else {
            session_unset();
        }
    }
    
    public static function get($key,$remove = false){
        
        $data = null;
        
        if(isset($_SESSION[$key])){
            $data = unserialize($_SESSION[$key]);
            
            if($remove){
                unset($_SESSION[$key]);
            }
        }
        
        return $data;
    }
    
    public static function regenerate($deleteOld=true){
        @session_regenerate_id($deleteOld);   
    }
    
    public static function save($data,$key){
        $_SESSION[$key] = serialize($data);
    }
    
    public static function set($key,$data){
        $_SESSION[$key] = serialize($data);
    }
}
