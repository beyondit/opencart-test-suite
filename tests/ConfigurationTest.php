<?php

require_once '../src/OpenCartTest.php';
require_once 'SampleTest.php';
require_once 'SampleAdminTest.php';

class ConfigurationTest extends PHPUnit_Framework_TestCase {
	
	public function testConfigurationLoading() {

		$this->assertStringEndsWith('/admin/config.php', SampleAdminTest::getConfigurationPath());
		$this->assertStringEndsWith('./config.php', SampleTest::getConfigurationPath());

	}
	
	public function testBootstrapASampleOpenCartTest() {			
		
		SampleTest::$_OPENCART = dirname(dirname(__DIR__)) . "/";		
		$test = new SampleTest();
		
	}
	
}