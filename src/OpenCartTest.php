<?php

class OpenCartTest extends PHPUnit_Framework_TestCase
{
	static $loaded = false;
    static $registry;

    public static function isAdmin()
    {
        return is_int(strpos(get_called_class(),"AdminTest"));
    }

	public static function loadConfiguration()
	{
		if (!isset($_ENV['OC_ROOT'])) {
			throw new \Exception('OC_ROOT environment variable needs to be set');
		}

		$config_path = $_ENV['OC_ROOT'] . (self::isAdmin() === false ? '' : 'admin/') . 'config.php';

		if (file_exists($config_path)) {
			require_once($config_path);
		} else {
			throw new Exception("Missing config file at: " .$config_path);
		}
	}

    public static function loadOpenCart()
    {
        if (!self::$loaded) {
            define('VERSION','2.2.0.0');

            $application_config = 'test-config';
            $_SERVER['SERVER_PORT']     = 80;
            $_SERVER['SERVER_PROTOCOL'] = 'CLI';
            $_SERVER['REQUEST_METHOD']  = 'GET';
            $_SERVER['REMOTE_ADDR']     = '127.0.0.1';

            ob_start();
            self::loadConfiguration();
            require_once(DIR_SYSTEM . 'startup.php');
            require_once(DIR_SYSTEM . 'framework.php');
            ob_end_clean();

            $session = new stdClass();
            $session->data = array();
            $session->session_id = bin2hex(openssl_random_pseudo_bytes(16));
            $session->getId = function() use ($session) {
                return $session->session_id;
            };

            self::$registry = $registry;
            self::$registry->set('controller',$controller);
            self::$registry->set('session',$session);

            self::$loaded = true;
        }
    }

    public function __get($name)
    {
        return self::$registry->get($name);
    }

    public function __construct()
	{
		self::loadOpenCart();
	}

	public function dispatchAction($route, $request_method = 'GET', $data = array())
	{
		if ($request_method != 'GET' && $request_method != 'POST') {
			$request_method = 'GET';
		}

		foreach ($data as $key => $value) {
			$this->request->{strtolower($request_method)}[$key] = $value;
		}

		$this->request->server['REQUEST_METHOD'] = $request_method;
		$this->controller->dispatch(new Action($route), new Action($this->config->get('action_error')));

		return $this->response;
	}

	public function loadModel($route)
	{
		$this->load->model($route);
		$parts = explode("/",$route);

		$model = 'model';
		foreach ($parts as $part) {
			$model .= "_" . $part;
		}

		return $this->$model;
	}

	public function login($username, $password, $override = false)
	{
		$logged = false;

		if (!$this->isAdmin() && ($logged = $this->customer->login($username, $password, $override))) { // login as customer
			$this->session->data['customer_id'] = $this->customer->getId();
		} elseif ($logged = $this->user->login($username, $password)) {
			$this->session->data['user_id'] = $this->user->getId();
			$this->request->get['token'] = $this->session->data['token'] = bin2hex(openssl_random_pseudo_bytes(16));
		}

		return $logged;
	}
		
}