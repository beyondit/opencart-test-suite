# OpenCart Testing Suite

## Motivation
The development of custom extensions for OpenCart gets tedious at the point where unit testing needs to be done. The intend of this project is to provide a simple approach for setting up a test suite for custom OpenCart development.

## Roadmap / Ideas / ...
* Provide a simple approach for testing custom controllers, models, extensions
* Provide a simple acceptance testing approach for selenium
* Provide a simple approach for running tests on a CI-Server (like Jenkins)
* ...

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
		"beyondit/opencart-test-suite": "0.2.0"
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
	public function testAddingASpecificProductToTheCart() {
		
		$controller = $this->loadControllerByRoute("checkout/cart");
		$this->request->post['product_id'] = 28;

		$controller->add();
		
		// use $this->getOutput() to access the rendered response as a string
		$output = json_decode($this->getOutput(),true);	
		
		$this->assertTrue(isset($output['success']) && isset($output['total']));
		$this->assertEquals(1,preg_match('/HTC Touch HD/', $output['success']));

	}	
}
```

### Testing With Logged In Customers
```php
class ControllerAccountWishListTest extends OpenCartTest {	
	public function testTheContentsOfALoggedInCustomersWishList() {
		
		$controller = $this->loadControllerByRoute("account/wishlist");	

		$this->customerLogin('mycustomer@example.com','password');
		$controller->index();
		
		$this->assertEquals(1,preg_match('/Your wish list is empty./', $this->getOutput()));
	}	
}
```

### Testing Classes from Admin
In order to test classes inside the admin folder just call your test class ending with `AdminTest` e.g. `ModelCatalogCategoryAdminTest`

### Testing With Selenium
Running Acceptance (Functional) Tests with Selenium requires a standalone selenium server on your machine.
The server can be downloaded from [here](http://code.google.com/p/selenium/downloads/list). Before starting your Selenium Tests
you have to run the standalone server: `java -jar selenium-server-standalone-2.32.0.jar`. Writing Selenium Tests requires you to extend the OpenCartSeleniumTest class.

```php
class SeleniumShoppingCartTest extends OpenCartSeleniumTest {	
    
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl('http://localhost/opencart/');
    }
 
    public function testIfTheRightPriceIsSetForAProductInTheCart()
    {
       
    	$this->url("index.php?route=product/product&product_id=43");
    	
        $this->clickOnElement("button-cart");
        
        $this->url("index.php?route=checkout/cart");
        
        $element = $this->byCssSelector(".cart-info tbody .price");

        $this->assertEquals("$589.50", $element->text());
              
    }
}
```

## Note
This project is at an initial stage, however already provides lots of convenience for testing OpenCart Components and can increase development productivity. Feel free to provide some feedback!
