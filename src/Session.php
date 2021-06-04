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
            return session_destroy();
        } else {
            return session_unset();
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
     * @param string $key
     * @return boolean
     */
    public static function remove($key)
    {

        if (array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key]);
            return true;
        }
        return false;
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
