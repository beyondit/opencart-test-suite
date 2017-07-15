<?php

use PHPUnit\Framework\TestCase;

class OpenCartTest extends TestCase
{
    static $loaded = false;
    static $registry;
    private static $is_admin = null;

    public function setUp()
    {
        if (!self::$loaded) {
            $application_config = getenv('TEST_CONFIG');
            $_SERVER['SERVER_PORT'] = 80;
            $_SERVER['SERVER_PROTOCOL'] = 'CLI';
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

            ob_start();
            self::loadConfiguration();
            require_once(DIR_SYSTEM . 'startup.php');
            require(DIR_SYSTEM . 'framework.php');
            ob_end_clean();

            self::$registry = $registry;
            self::$registry->set('controller', $route);

            self::$loaded = true;
        }
    }

    public static function isAdmin()
    {
        if (is_null(self::$is_admin)) {
            self::$is_admin = is_int(strpos(get_called_class(), "AdminTest"));
        }
        return self::$is_admin;
    }

    public static function loadConfiguration()
    {
        if (!isset($_ENV['OC_ROOT'])) {
            throw new \Exception('OC_ROOT environment variable needs to be set');
        }

        // Path needs / at the end
        if (substr($_ENV['OC_ROOT'], -1) != DIRECTORY_SEPARATOR) {
            $_ENV['OC_ROOT'] .= DIRECTORY_SEPARATOR;
        }

        $config_path = $_ENV['OC_ROOT'] . (self::isAdmin() === false ? '' : 'admin/') . 'config.php';

        if (file_exists($config_path)) {
            require_once($config_path);
        } else {
            throw new Exception("Missing config file at: " . $config_path);
        }
    }

    public function __get($name)
    {
        return self::$registry->get($name);
    }

    public function dispatchAction($route, $request_method = 'GET', $data = array())
    {
        if ($request_method != 'GET' && $request_method != 'POST') {
            $request_method = 'GET';
        }

        foreach ($data as $key => $value) {
            $this->request->{strtolower($request_method)}[$key] = $value;
        }
        if (self::isAdmin()) {
            $this->request->get['user_token'] = $this->session->data['user_token'];
        }
        $this->request->cookie['language'] = 'en-gb';
        $this->request->cookie['currency'] = 'USD';

        $this->request->get['route'] = $route;
        $this->request->server['REQUEST_METHOD'] = $request_method;
        $this->controller->dispatch(new Action($route), new Action($this->config->get('action_error')));

        return $this->response;
    }

    public function loadModel($route)
    {
        $this->load->model($route);
        $parts = explode("/", $route);

        $model = 'model';
        foreach ($parts as $part) {
            $model .= "_" . $part;
        }

        return $this->$model;
    }

    public function login($username, $password, $override = false)
    {
        $logged = false;

        if (!self::isAdmin() && ($logged = $this->customer->login($username, $password, $override))) { // login as customer
            $this->session->data['customer_id'] = $this->customer->getId();
        } elseif ($logged = $this->user->login($username, $password)) {
            $this->session->data['user_id'] = $this->user->getId();
            $this->session->data['user_token'] = bin2hex(openssl_random_pseudo_bytes(16));
        }

        return $logged;
    }

    public function logout()
    {
        if (self::isAdmin()) {
            $this->user->logout();
            unset($this->session->data['user_id']);
            unset($this->session->data['user_token']);
        } else {
            $this->customer->logout();
            unset($this->session->data['customer_id']);
        }
    }
    
    public function tearDown()
    {
        $is_admin = self::$is_admin;
        self::$is_admin = null;
        self::$loaded = false;
    }

}