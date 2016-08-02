[![Build Status](https://travis-ci.org/beyondit/opencart-test-suite.svg?branch=master)](https://travis-ci.org/beyondit/opencart-test-suite)

# OpenCart Testing Suite

## Motivation
The intend of this project is to provide a simple approach for setting up a test suite for custom OpenCart development. 

## Getting started from scratch

 - Create a new OpenCart project with composer: `composer create-project opencart/opencart`
 - Install OpenCart, easiest via command line: `php upload/install/cli_install.php` (be sure you have respective execute permissions on the file)
 - Navigate into the newly created `opencart` folder and add `opencart-test-suite` as a dependency: `composer require beyondit/opencart-test-suite --dev`
 - Create a `tests` folder and add respective tests (see examples below)
 - Add a `phpunit.xml` which includes testsuites (e.g. admin and catalog) and set an env variable to the opencart root directory (see example phpunit.xml below)
 - Copy `test-config.php` from project to `upload/system/config/test-config.php` and `test-catalog-startup.php` to `upload/catalog/controller/startup/test_startup.php`
 - Now tests can be run via `vendor/bin/phpunit` command

__A much easier way to get started is to use our project template, which offers many conveniences out of the box: [Opencart Project Template](https://github.com/beyondit/opencart-project-template).__

## Example of a phpunit.xml

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="vendor/autoload.php">
    <testsuites>
        <testsuite name="catalog-tests">
            <file>./tests/SampleTest.php</file>
        </testsuite>        
    </testsuites>
    <php>
        <env name="OC_ROOT" value="/../opencart/root-folder" />
    </php>
</phpunit>
```
			
## Test Examples

### Testing a Model

```php
class ModelCatalogManufacturerTest extends OpenCartTest
{	
	public function testASpecificManufacturer()
	{
		
		// load the manufacturer model
		$model = $this->loadModel("catalog/manufacturer");
		$manufacturer = $model->getManufacturer(5);		
		
		// test a specific assertion
		$this->assertEquals('HTC', $manufacturer['name']);
		
	}	
}
```

### Testing a Controller
```php
class ControllerCheckoutCartTest extends OpenCartTest
{	
	public function testAddingASpecificProductToTheCart()
	{
			
		$response = $this->dispatchAction('checkout/cart/add','POST',['product_id' => 28]);
        $output = json_decode($response->getOutput(),true);
        
        $this->assertTrue(isset($output['success']) && isset($output['total']));
        $this->assertRegExp('/HTC Touch HD/', $output['success']);
        
	}	
}
```

### Testing with logged in Customers
```php
class ControllerAccountEditTest extends OpenCartTest {  
    public function testEditAccountWithLoggedInCustomer() {

        $this->login('somebody@test.com','password');
        
        $response = $this->dispatchAction('account/edit');
        $this->assertRegExp('/Your Personal Details/',$response->getOutput());
        
        $this->logout();
        
    }   
}
```

### Testing with logged in Users inside Admin

In order to test classes inside the admin folder just call your test class ending with `AdminTest` e.g. `ModelCatalogCategoryAdminTest`

```php
class ControllerCommonDashboardAdminTest extends OpenCartTest {  
    public function testShowDashboardWithLoggedInUser() {

        $this->login('admin','admin');
        
        $response = $this->dispatchAction('common/dashboard');
        $this->assertRegExp('/Total Sales/', $response->getOutput());
        
        $this->logout();
        
    }   
}
```

