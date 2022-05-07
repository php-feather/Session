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
     * @return bool
     */
    public static function clear()
    {
        return session_unset();
    }

    /**
     *
     * @return bool
     */
    public static function flush()
    {
        session_unset();
        return session_destroy();
    }

    /**
     *
     * @param string $key
     * @param mixed $default
     * @param bool $remove
     * @return mixed
     */
    public static function get($key, $default = null, $remove = false)
    {
        $data = $default;

        if (isset($_SESSION[$key])) {
            $data = $_SESSION[$key];

            if ($remove) {
                unset($_SESSION[$key]);
            }
        }

        return $data;
    }

    /**
     *
     * @return bool
     */
    public static function has()
    {
        return array_key_exists($key, $_SESSION);
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
        $_SESSION[$key] = $data;
    }

    /**
     *
     * @param string $key
     * @param mixed $data
     */
    public static function set($key, $data)
    {
        $_SESSION[$key] = $data;
    }

}
