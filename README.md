# OpenCart Testing Suite

## Motivation
The development of custom extensions for OpenCart gets tedious at the point where unit testing needs to be done. The intend of this project is to provide a simple approach for setting up a test suite for custom OpenCart development.

## Roadmap / Ideas / ...
* Provide a simple approach for testing custom controllers, models, extensions
* Provide a simple acceptance testing approach for selenium

## Usage

### Prerequisits
* Install [composer](http://getcomposer.org/) on your machine
* An installed version of opencart

### Steps to use
* create a folder in the root of your OpenCart installation (e.g. tests/)
* add a composer.json file wihtin the folder, and add the following:

```javascript
{
	"require": {
		"beyondit/opencart-test-suite": "0.1.4"
	}
}
```
* run `composer update` to download the necessary project dependencies
* create a UnitTest and extend it from OpenCartTest class, e.g.:

```php
class MyTest extends OpenCartTest {	
    public function testSomething() {			
    }	
}
```
* run `vendor/bin/phpunit MyTest` inside your test folder
			
## Examples

### Testing a Model

```php
class ModelCatalogManufacturerTest extends OpenCartTest {	
	public function testASpecificManufacturer() {
		
		// load the manufacturer model
		$model = $this->loadModelByRoute("catalog/manufacturer");
		$manufacturer = $model->getManufacturer(5);		
		
		// test a specific assertion
		$this->assertEquals('HTC', $manufacturer['name']);
		
	}	
}
```

### Testing a Controller
```php
class ControllerAccountWishListTest extends OpenCartTest {	
	public function testASpecificManufacturer() {
		
		// load the the wishlist controller within accout folder
		$controller = $this->loadControllerByRoute("account/wishlist");
		
		// set some test params for your request
		$this->request->post['product_id'] = 123;
		
		$controller->add();
		
		// get the response
		$response = $this->response;
		
		/*
		 * Unfortunately the current version of OpenCart doesn't provide a getOuput() Method
		 * inside the response class, if you add it to your response class you 
		 * could do custom assertions of your actual response, otherwise you can
		 * only print the response to your command line:
		 *  
		 */
		
		$response->output();
	}	
}
```
### Testing Classes from Admin
In order to test classes inside the admin folder just call your test class ending with `AdminTest` e.g. `ModelCatalogCategoryAdminTest`

## Note
This project is at an initial stage, however already provides lots of convenience for testing OpenCart Components and can increase development productivity. Feel free to provide some feedback!
