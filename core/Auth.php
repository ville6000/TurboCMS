<?php

namespace TurboCMS;

/**
 * Class Auth
 *
 * @package TurboCMS
 */
class Auth
{
    /**
     * TurboCMS settings
     * @var array $settings
     */
    private $settings;

    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    /**
     * Is user authorized
     *
     * @return bool
     */
    public function isAuthorized()
    {
        if (!isset($_SESSION['turbo_cms_login']) ||
            !password_verify($this->settings['passphrase'], $_SESSION['turbo_cms_login'])) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Login
     *
     * @param $passphrase
     *
     * @return bool
     */
    public function login($passphrase)
    {
        if ($passphrase == $this->settings['passphrase']) {
            $_SESSION['turbo_cms_login'] = password_hash($this->settings['passphrase'], PASSWORD_BCRYPT);
            return true;
        } else {
            return false;
        }
    }
}