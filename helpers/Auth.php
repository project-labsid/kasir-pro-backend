<?php
/**
 * Authentication Helper
 */

require_once CONFIG_PATH . '/constants.php';
require_once __DIR__ . '/Security.php';

class Auth {
    private static $user = null;

    /**
     * Start session
     */
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
            Security::setSecurityHeaders();
        }
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Get current user
     */
    public static function user() {
        if (self::$user === null && self::isLoggedIn()) {
            self::$user = $_SESSION['user'] ?? null;
        }
        return self::$user;
    }

    /**
     * Get user ID
     */
    public static function userId() {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get user role
     */
    public static function role() {
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Check if user is admin
     */
    public static function isAdmin() {
        return self::role() === ROLE_ADMIN;
    }

    /**
     * Check if user is cashier
     */
    public static function isCashier() {
        return self::role() === ROLE_CASHIER;
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole($role) {
        return self::role() === $role;
    }

    /**
     * Login user
     */
    public static function login($userId, $username, $role, $userData = []) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['user_role'] = $role;
        $_SESSION['user'] = $userData;
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['session_id'] = session_id();
        
        self::$user = $userData;
    }

    /**
     * Logout user
     */
    public static function logout() {
        $_SESSION = [];
        if (ini_get('session.use_cookies') === '1') {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
        self::$user = null;
    }

    /**
     * Check session timeout
     */
    public static function checkSessionTimeout() {
        if (!self::isLoggedIn()) {
            return false;
        }

        $lastActivity = $_SESSION['last_activity'] ?? 0;
        $currentTime = time();
        $timeout = SESSION_TIMEOUT;

        if (($currentTime - $lastActivity) > $timeout) {
            self::logout();
            return false;
        }

        $_SESSION['last_activity'] = $currentTime;
        return true;
    }

    /**
     * Require login
     */
    public static function requireLogin() {
        if (!self::isLoggedIn() || !self::checkSessionTimeout()) {
            header('Location: /login.php');
            exit();
        }
    }

    /**
     * Require admin
     */
    public static function requireAdmin() {
        self::requireLogin();
        if (!self::isAdmin()) {
            http_response_code(403);
            die('Access Denied');
        }
    }

    /**
     * Require cashier
     */
    public static function requireCashier() {
        self::requireLogin();
        if (!self::isCashier()) {
            http_response_code(403);
            die('Access Denied');
        }
    }

    /**
     * Require specific role
     */
    public static function requireRole($role) {
        self::requireLogin();
        if (!self::hasRole($role)) {
            http_response_code(403);
            die('Access Denied');
        }
    }
}

// Initialize auth
Auth::init();
