# OpenCart Testing Suite

## Motivation
The intend of this project is to provide a simple approach for setting up a test suite for custom OpenCart development.
			
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
class ControllerAccountWishListTest extends OpenCartTest
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
