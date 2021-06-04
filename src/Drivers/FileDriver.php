<?php

namespace Feather\Session\Drivers;

use Feather\Session\SessionException;

/**
 * Description of File
 *
 * @author fcarbah
 */
class FileDriver extends Driver
{

    protected $path;

    /**
     *
     * @param string $sessionPath
     * @throws SessionException
     */
    public function __construct($sessionPath)
    {

        $this->path = $sessionPath;

        if (!is_dir($this->path)) {
            throw new SessionException($sessionPath . ' is not a directory', 100);
        }

        if (!is_writable($this->path)) {
            throw new SessionException($sessionPath . ' is not a writeable directory', 101);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function activate()
    {
        session_cache_limiter('private');
        session_save_path($this->path);
        ini_set('session.gc_probability', 1);
    }

}
