<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Feather\Session\Drivers;

/**
 * Description of RedisDriver
 *
 * @author fcarbah
 */
class RedisDriver implements SessionHandlerContract{
    
    protected $path;
    
    public function __construct($server,$port='6379',$scheme='tcp',array $connOptions=array()) {
        
        $savePath = $scheme.'://'.$server.':'.$port;
        
        $optStr='';
        foreach ($connOptions as $key=>$val){
            $optStr .="$key=$val&";
        }
        
        $this->path= $savePath.'?'.substr($optStr, 0,strlen($optStr)-1);

    }
    
    public function activate() {
        ini_set('session.save_handler', 'redis');
        session_save_path($this->path);
    }
    
}
