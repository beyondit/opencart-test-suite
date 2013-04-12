<?php

// TODO: check for a better way to get the root of the opencart installation
define('OC_ROOT',__DIR__ . '/../../../../../');

class OpenCartTest extends PHPUnit_Framework_TestCase {
	
	protected $registry;
	protected $front;
	
	public static $_OPENCART = OC_ROOT;
	
	public static function getConfigurationPath() {				
		$admin = strpos(get_called_class(),"AdminTest");
		return self::$_OPENCART . ($admin === false ? '' : 'admin/') . 'config.php' ;
	}
	
	public function __get($key) {
		return $this->registry->get($key);
	}
	
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}	
	
	public function loadConfiguration() {
		
		// either load admin or catalog config.php		
		$path = self::getConfigurationPath();
		
		// Configuration
		if (file_exists($path)) {
			require_once($path);
		} else {
			throw new Exception('OpenCart has to be installed first!');
		}		
	}
	
	public function __construct() {
		
		$this->loadConfiguration();
				
		// Startup
		require_once(DIR_SYSTEM . 'startup.php');
		
		// Application Classes
		require_once(DIR_SYSTEM . 'library/customer.php');
		require_once(DIR_SYSTEM . 'library/affiliate.php');
		require_once(DIR_SYSTEM . 'library/currency.php');
		require_once(DIR_SYSTEM . 'library/tax.php');
		require_once(DIR_SYSTEM . 'library/weight.php');
		require_once(DIR_SYSTEM . 'library/length.php');
		require_once(DIR_SYSTEM . 'library/cart.php');
		
		// Registry
		$this->registry = new Registry();
		
		// Loader
		$loader = new Loader($this->registry);
		$this->registry->set('load', $loader);
		
		// Config
		$config = new Config();
		$this->registry->set('config', $config);
		
		// Database
		$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		$this->registry->set('db', $db);
		
		// assume a HTTP connection
		$store_query = $db->query("SELECT * FROM " . DB_PREFIX . "store WHERE REPLACE(`url`, 'www.', '') = '" . $db->escape('http://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/') . "'");
		
		if ($store_query->num_rows) {
			$config->set('config_store_id', $store_query->row['store_id']);
		} else {
			$config->set('config_store_id', 0);
		}
		
		// Settings
		$query = $db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '0' OR store_id = '" . (int)$config->get('config_store_id') . "' ORDER BY store_id ASC");
		
		foreach ($query->rows as $setting) {
			if (!$setting['serialized']) {
				$config->set($setting['key'], $setting['value']);
			} else {
				$config->set($setting['key'], unserialize($setting['value']));
			}
		}
		
		if (!$store_query->num_rows) {
			$config->set('config_url', HTTP_SERVER);
			$config->set('config_ssl', HTTPS_SERVER);
		}
		
		// Url
		$url = new Url($config->get('config_url'), $config->get('config_secure') ? $config->get('config_ssl') : $config->get('config_url'));
		$this->registry->set('url', $url);
		
		// Request
		$request = new Request();
		$this->registry->set('request', $request);
		
		// Response
		$response = new Response();
		$response->addHeader('Content-Type: text/html; charset=utf-8');
		$response->setCompression($config->get('config_compression'));
		$this->registry->set('response', $response);
		
		// Cache
		$cache = new Cache();
		$this->registry->set('cache', $cache);
		
		// Session
		$session = new Session();
		$this->registry->set('session', $session);
		
		// Language Detection
		$languages = array();
		
		$query = $db->query("SELECT * FROM `" . DB_PREFIX . "language` WHERE status = '1'");
		
		foreach ($query->rows as $result) {
			$languages[$result['code']] = $result;
		}
		
		$detect = '';
		
		if (isset($request->server['HTTP_ACCEPT_LANGUAGE']) && $request->server['HTTP_ACCEPT_LANGUAGE']) {
			$browser_languages = explode(',', $request->server['HTTP_ACCEPT_LANGUAGE']);
		
			foreach ($browser_languages as $browser_language) {
				foreach ($languages as $key => $value) {
					if ($value['status']) {
						$locale = explode(',', $value['locale']);
		
						if (in_array($browser_language, $locale)) {
							$detect = $key;
						}
					}
				}
			}
		}
		
		if (isset($session->data['language']) && array_key_exists($session->data['language'], $languages) && $languages[$session->data['language']]['status']) {
			$code = $session->data['language'];
		} elseif (isset($request->cookie['language']) && array_key_exists($request->cookie['language'], $languages) && $languages[$request->cookie['language']]['status']) {
			$code = $request->cookie['language'];
		} elseif ($detect) {
			$code = $detect;
		} else {
			$code = $config->get('config_language');
		}
		
		if (!isset($session->data['language']) || $session->data['language'] != $code) {
			$session->data['language'] = $code;
		}
		
		if (!isset($request->cookie['language']) || $request->cookie['language'] != $code) {
			setcookie('language', $code, time() + 60 * 60 * 24 * 30, '/', $request->server['HTTP_HOST']);
		}
		
		$config->set('config_language_id', $languages[$code]['language_id']);
		$config->set('config_language', $languages[$code]['code']);
		
		// Language
		$language = new Language($languages[$code]['directory']);
		$language->load($languages[$code]['filename']);
		$this->registry->set('language', $language);
		
		// Document
		$this->registry->set('document', new Document());
		
		// Customer
		$this->registry->set('customer', new Customer($this->registry));
		
		// Affiliate
		$this->registry->set('affiliate', new Affiliate($this->registry));
		
		if (isset($request->get['tracking'])) {
			setcookie('tracking', $request->get['tracking'], time() + 3600 * 24 * 1000, '/');
		}
		
		// Currency
		$this->registry->set('currency', new Currency($this->registry));
		
		// Tax
		$this->registry->set('tax', new Tax($this->registry));
		
		// Weight
		$this->registry->set('weight', new Weight($this->registry));
		
		// Length
		$this->registry->set('length', new Length($this->registry));
		
		// Cart
		$this->registry->set('cart', new Cart($this->registry));
		
		// Encryption
		$this->registry->set('encryption', new Encryption($config->get('config_encryption')));
		
		// Front Controller
		$this->front = new Front($this->registry);
		
		// TODO: check if preactions are neccessary for testing purpouses...
		
		// SEO URL's
		// $this->front->addPreAction(new Action('common/seo_url'));		
		// Maintenance Mode
		// $this->front->addPreAction(new Action('common/maintenance'));
	}	
	
	public function customerLogin($user,$password,$override=false) {
		
		// set a REMOTE_ADDR for the customer ...
		$this->request->server['REMOTE_ADDR'] = '127.0.0.1';
		$logged = $this->customer->login($user,$password,$override);		
				
		if (!$logged) {
			throw new Exception('Could not login customer');	
		}		
	}
	
	public function customerLogout() {		
		if ($this->customer->isLogged()) {		
			$this->customer->logout();		
		}
	}
	
	
	/*
	 * A Controller Object is returned, the actual action is not executed
	 * 
	 * @throws Exception if controller doesn't exist
	 */
	public function loadControllerByRoute($route) {
		$action = new Action($route);
		
		// copied from front controller to resolve the action controller and return it:
		if (file_exists($action->getFile())) {
			require_once($action->getFile());
				
			$class = $action->getClass();
		
			return new $class($this->registry);				
		}	

		throw new Exception("Controller doesn't exist!");
		
	}
	
	public function dispatchAction($route) {
		
		// Router
		if (!empty($route)) {
			$action = new Action($route);
		} else {
			$action = new Action('common/home');
		}
		
		// Dispatch
		$this->front->dispatch($action, new Action('error/not_found'));
		
		return $this->response;
	}
	
	public function loadModelByRoute($route) {
		$this->load->model($route);		
		$parts = explode("/",$route);
		
		$model = 'model';
				
		foreach ($parts as $part) {
			$model .= "_" . $part;
		}
		
		return $this->$model;
		
	}
	
		
}