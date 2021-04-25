<?php

namespace Feather\Session\Drivers;

/**
 *
 * @author fcarbah
 */
interface ISessionHandler
{

    /**
     * Boot session driver
     */
    public function activate();

    /**
     * Starts Session
     * @param array $options
     * @return boolean
     */
    public function start(array $options);

    /**
     * Check if session has started or not
     * @return boolean
     */
    public function isStarted();
}
