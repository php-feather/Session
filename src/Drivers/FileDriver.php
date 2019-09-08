<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Feather\Session\Drivers;
use Feather\Session\SessionException;

/**
 * Description of File
 *
 * @author fcarbah
 */
class FileDriver  implements SessionHandlerContract{
    
    protected $path;
    
    public function __construct($sessionPath) {
        
        $this->path = $sessionPath;
        
        if(!is_dir($this->path)){
            throw new SessionException($sessionPath.' is not a directory', 100);
        }

        if(!is_writable($this->path)){
            throw new SessionException($sessionPath.' is not a writeable directory', 101);
        }
        
    }
    
    public function activate() {
        session_save_path($this->path);
        ini_set('session.gc_probability', 1);
    }
    
}
