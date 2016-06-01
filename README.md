[![Build Status](https://travis-ci.org/beyondit/opencart-test-suite.svg?branch=master)](https://travis-ci.org/beyondit/opencart-test-suite)

# OpenCart Testing Suite

## Motivation
The intend of this project is to provide a simple approach for setting up a test suite for custom OpenCart (v2.2.0.0) development. 

## Getting started
The easiest way to get started, is to use our [Opencart Project Template](https://github.com/beyondit/opencart-project-template).
			
## Examples

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

