<?php

require_once '../src/OpenCartSeleniumTest.php';

class SeleniumTest extends OpenCartSeleniumTest {
	
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