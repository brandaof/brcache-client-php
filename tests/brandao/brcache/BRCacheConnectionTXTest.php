<?php
require_once __DIR__ . '\brandao\brcache\BRCacheConnection.php';

class BRCacheConnectionTXTest extends PHPUnit_Framework_TestCase{
	
	private $SERVER_HOST	= "localhost";
	
	private $SERVER_PORT	= 1044;
	
	private $KEY			= "teste";
	
	private $VALUE			= "value";
	
	private $VALUE2			= "val";
	
	protected function setUp(){
	}
	
	protected function tearDown(){
	}
	
	/* replace */
	
	public function testReplaceExact(){
		$prefixKEY = "testReplaceExact:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
		
		$this->assertFalse($con->replaceValue(
			$prefixKEY . $this->KEY, 
			$this->VALUE, 
			$this->VALUE2, 
			function($a, $b){
				return strcmp($a,$b);
			}, 0, 0));
		
	}
	
	public function testReplaceExactSuccess(){
		$prefixKEY = "testReplaceExactSuccess:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
				
		$con->put($prefixKEY . KEY, $this->VALUE, 0, 0);
		$this->assertEquals($this->VALUE, $con->get($prefixKEY . $this->KEY));
		$this->assertTrue($con->replace(
			$prefixKEY . $this->KEY, 
			$this->VALUE, 
			$this->VALUE2, 
			function($a, $b){
				return strcmp($a,$b);
			}, 0, 0));
		
		$this->assertEquals($this->VALUE2, $con->get($prefixKEY . $this->KEY));
	}
	
	/* putIfAbsent */
	
	public function testputIfAbsent(){
		$prefixKEY = "testputIfAbsent:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
				
		$this->assertNull($con->putIfAbsent($prefixKEY . $this->KEY, $this->VALUE, 0, 0));
		$this->assertEquals($this->VALUE, $con->get($prefixKEY . $this->KEY));
	}
	
	public function testputIfAbsentExistValue(){
		$prefixKEY = "testputIfAbsentExistValue:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
				
		$con->put($prefixKEY + $this->KEY, $this->VALUE, 0, 0);
		$this->assertEquals($this->VALUE, $con->putIfAbsent($prefixKEY + $this->KEY, $this->VALUE2, 0, 0));
		$this->assertEquals($this->VALUE, $con->get($prefixKEY . $this->KEY));
	}
	
	/* remove */
	
	public function testRemoveExact(){
		$prefixKEY = "testRemoveExact:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
				
		$this->assertNull($con->get($prefixKEY . $this->KEY));
		$this->assertFalse($con->remove($prefixKEY . $this->KEY, $this->VALUE));
		
		$con->put($prefixKEY . $this->KEY, $this->VALUE, 0, 0);
		
		$this->assertEquals($this->VALUE, $con->get($prefixKEY . $this->KEY));
		
		$this->assertFalse($con->remove(
			prefixKEY . KEY, 
			VALUE2, 
			function($a, $b){
				return strcmp($a,$b);
			}));
			
		$this->assertTrue($con->remove(
			$prefixKEY . $this->KEY, 
			$this->VALUE, 
			function($a, $b){
				return strcmp($a,$b);
			}));
			
		$this->assertNull($con->get($prefixKEY . $this->KEY));
	}

}
