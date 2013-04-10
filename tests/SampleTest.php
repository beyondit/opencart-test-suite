<?php

require_once '../src/OpenCartTest.php';
OpenCartTest::$_OPENCART = dirname(dirname(__DIR__)) . "/";

class SampleTest extends OpenCartTest {
	
	public function testLoadingExamplaryController() {
		
		$controller = $this->loadControllerByRoute('product/product');			
		$this->assertInstanceOf('ControllerProductProduct', $controller);
		
	}
	
	public function testDispatchingExamplaryAction() {
		
		$response = $this->dispatchAction('product/product');
		$this->assertInstanceOf('Response', $response);
		
	}
	
	public function testLoadingExamplaryModel() {
		
		$model = $this->loadModelByRoute('catalog/category');
		$this->assertInstanceOf('ModelCatalogCategory', $model);
		
	}
	
}