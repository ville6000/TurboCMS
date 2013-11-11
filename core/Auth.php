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
        if (!$this->isPassphraseAuthorized() && !$this->isEmailAuthorized()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Is user authorized with passphrase
     *
     * @return bool
     */
    public function isPassphraseAuthorized()
    {
        if (!isset($_SESSION['turbo_cms_login']) ||
            !password_verify($this->settings['passphrase'], $_SESSION['turbo_cms_login'])) {

            return false;
        } else {
            return true;
        }
    }

    /**
     * Is user authorized with email
     *
     * @return bool
     */
    public function isEmailAuthorized()
    {
        if (!isset($_SESSION['turbo_cms_login_email'])) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Login user with passphrase
     *
     * @param $passphrase
     *
     * @return bool
     */
    public function passphraseLogin($passphrase)
    {
        if ($passphrase == $this->settings['passphrase']) {
            $_SESSION['turbo_cms_login'] = password_hash($this->settings['passphrase'], PASSWORD_BCRYPT);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Start user login with email token
     *
     * @param $email
     *
     * @return string|bool
     */
    public function initEmailLogin($email = '')
    {
        // Get allowed user email data
        $allowedEmails = $this->settings['allowed_emails'];

        // Check that data was found and given email is allowed
        if (empty($allowedEmails) || !in_array($email, $allowedEmails)) {
            // Email not found, reset login
            $this->destroySession();

            return false;
        }

        // Clean up session
        $this->destroySession();
        session_start();

        // Generate token
        $token = md5(uniqid('TurboCMS_', true));

        // Save token and timestamp to session
        $_SESSION['turbo_cms_login_email'] = $email;
        $_SESSION['turbo_cms_login_email_token'] = $token;
        $_SESSION['turbo_cms_login_email_timestamp'] = time();

        // Return token for sending
        return $token;
    }

    /**
     * Login user with email token
     *
     * @param $token
     *
     * @return bool
     */
    public function handleEmailLogin($token = '')
    {
        // Set data from session
        $email = $_SESSION['turbo_cms_login_email'];
        $sessionToken = $_SESSION['turbo_cms_login_email_token'];
        $timestamp = $_SESSION['turbo_cms_login_email_timestamp'];

        // Check for expired login token
        if (time() > ($timestamp + 900)) {
            // Token expired, reset login
            $this->destroySession();

            return false;
        }

        // Check if passed token matches one in session
        if ($token !== $sessionToken) {
            // Session token does not match, reset login
            $this->destroySession();

            return false;
        }
        
        // Clean up session before login
        unset($_SESSION['turbo_cms_login_email_token']);
        unset($_SESSION['turbo_cms_login_email_timestamp']);

        // Mark user logged in
        $_SESSION['turbo_cms_login_email'] = $email;

        return true;
    }

    /**
     * Destroy session and session data
     *
     * @return void
     */
    public function destroySession()
    {
        // Wipe runtime $_SESSION
        session_unset();

        // Wipe session storage file
        session_destroy();
    }
}