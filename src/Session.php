<?php

namespace Feather\Session;

/**
 * Description of Session
 *
 * @author fcarbah
 */
class Session
{

    /**
     *
     * @param boolean $destroy
     */
    public static function flush($destroy = false)
    {
        if ($destroy) {
            session_destroy();
        } else {
            session_unset();
        }
    }

    /**
     *
     * @param string $key
     * @param boolean $remove
     * @return mixed
     */
    public static function get($key, $remove = false)
    {

        $data = null;

        if (isset($_SESSION[$key])) {
            $data = unserialize($_SESSION[$key]);

            if ($remove) {
                unset($_SESSION[$key]);
            }
        }

        return $data;
    }

    /**
     *
     * @param boolean $deleteOld
     */
    public static function regenerate($deleteOld = true)
    {
        @session_regenerate_id($deleteOld);
    }

    /**
     *
     * @param mixed $data
     * @param string $key
     */
    public static function save($data, $key)
    {
        $_SESSION[$key] = serialize($data);
    }

    /**
     *
     * @param string $key
     * @param mixed $data
     */
    public static function set($key, $data)
    {
        $_SESSION[$key] = serialize($data);
    }

}
