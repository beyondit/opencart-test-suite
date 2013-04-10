# OpenCart Testing Suite

## Motivation
The development of custom extensions for OpenCart gets tedious at the point where unit testing needs to be done. The intend of this project is to provide a simple approach for setting up a test suite for custom OpenCart development.

## Roadmap / Ideas / ...
* Provide a simple approach for testing custom controllers, models, extensions
* Provide a simple acceptance testing approach for selenium

## Usage
The project is at the initial stage. And the approach how to set this up will probably change.  

### Prerequisits
* Install [composer](http://getcomposer.org/) on your machine
* An installed version of opencart

### Steps to use
* create a directory in the root of your OpenCart installation (e.g. tests/)
* create a composer.json file inside, and add the following:  
```javascript
	{  
	    "require": {  
		"beyondit/opencart-test-suite": "0.1.3"  
	    }  
	}
```
* create a phpunit.xml and add the following:  
```xml
	<phpunit bootstrap="vendor/autoload.php" colors="true">
	    <testsuites>
		<testsuite name="Add the name of your Testsuite">
		    <directory>./</directory>
		</testsuite>
	    </testsuites>
	</phpunit>
```
* create a UnitTest and extend it from OpenCartTest class, e.g.:  
```php
	class MyTest extends OpenCartTest {	
		public function testSomething() {			
		}	
	}
```
* run the following command to run the test (inside your test folder):  
```
	vendor/bin/phpunit MyTest
```


