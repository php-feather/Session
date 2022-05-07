<?php

namespace Feather\Session\Drivers;

/**
 * Description of Driver
 *
 * @author fcarbah
 */
abstract class Driver implements ISessionHandler
{

    private $validOptions = [
        'cache_expire', 'cache_limiter', 'cookie_domain', 'cookie_httponly',
        'cookie_lifetime', 'cookie_path', 'cookie_secure', 'cookie_samesite',
        'gc_divisor', 'gc_maxlifetime', 'gc_probability',
        'lazy_write', 'name', 'referer_check',
        'serialize_handler', 'use_strict_mode', 'use_cookies',
        'use_only_cookies', 'use_trans_sid', 'upload_progress.enabled',
        'upload_progress.cleanup', 'upload_progress.prefix', 'upload_progress.name',
        'upload_progress.freq', 'upload_progress.min_freq', 'url_rewriter.tags',
        'sid_length', 'sid_bits_per_character', 'trans_sid_hosts', 'trans_sid_tags',
    ];
    protected $started    = false;
    protected $sameSite   = false;

    /**
     *
     * @return boolean
     */
    public function isStarted()
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     *
     * @param array $options
     * @throws \RuntimeException
     */
    public function start(array $options)
    {
        if ($this->started) {
            return true;
        }

        if (headers_sent($file, $line)) {
            throw new \RuntimeException("Headers already sent. {$file} at line {$line}");
        }

        $this->setOptions($options);

        if (session_status() == PHP_SESSION_NONE) {
            if (!session_start()) {
                throw new \RuntimeException('Failed to start session');
            }
        }

        if ($this->sameSite) {
            $sessionCookie = $this->getSessionCookie();
            header("{$sessionCookie};SameSite={$this->sameSite}", false);
        }
        $this->started = true;
        return true;
    }

    /**
     *
     * @param array $cookies
     */
    protected function clearCookies(array $cookies)
    {
        header_remove('Set-Cookie');
        foreach ($cookies as $c) {
            header($c, false);
        }
    }

    /**
     *
     * @return string|null
     */
    protected function getSessionCookie()
    {
        $sessionCookiePrefix = urlencode(session_name()) . '=';
        $sessionCookieWithId = $sessionCookiePrefix . session_id();
        $otherCookies        = [];
        $sessionCookie       = null;

        foreach (headers_list() as $header) {
            if (stripos($header, 'set-cookie') !== 0) {
                continue;
            }

            if (stripos($header, $sessionCookiePrefix) === 11) {
                $sessionCookie = $header;

                if (stripos($header, $sessionCookieWithId) !== 11) {
                    $otherCookies[] = $header;
                }
            } else {
                $otherCookies[] = $header;
            }
        }

        if ($sessionCookie !== null) {
            $this->clearCookies($otherCookies);
        }

        return $sessionCookie;
    }

    /**
     *
     * @param array $options
     */
    protected function setOptions(array $options)
    {
        $options += [
            'cache_limiter'   => '',
            'cache_expire'    => 0,
            'use_cookies'     => 1,
            'lazy_write'      => 1,
            'use_strict_mode' => 1,
        ];

        $validOptions = array_flip($this->validOptions);

        foreach ($options as $option => $val) {

            if (($pos = stripos($option, 'session.')) === 0) {
                $option = substr($option, 8);
            }

            if ($option == 'cookie_samesite' && PHP_VERSION_ID < 70300) {
                $this->sameSite = true;
            }

            if (isset($validOptions[$option])) {
                ini_set($option !== 'url_rewriter.tags' ? "session.$option" : $option, $val);
            }
        }
    }

}
