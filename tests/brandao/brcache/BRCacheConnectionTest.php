<?php
require_once 'C:\develop\Apache2.4.18\htdocs\brcache-client-php\brandao\brcache\BRCacheConnection.php';

class BRCacheConnectionTest extends PHPUnit_Framework_TestCase{
	
	private $SERVER_HOST	= "localhost";
	
	private $SERVER_PORT	= 1044;
	
	private $KEY			= "teste";
	
	private $VALUE			= "value";
	
	private $VALUE2			= "val";
	
	protected function setUp(){
	}
	
	protected function tearDown(){
	}

	public function testClose(){
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
		$this->assertFalse($con->isClosed());
		$con->close();
		$this->assertTrue($con->isClosed());
	}
	
	public function testCloseFail(){
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, true);
		$this->assertFalse($con->isClosed());
	
		try{
			$con->close();
			$this->fail("expected CacheException");
		}
		catch(CacheException $e){
		}
	}
	
	/* replace */

	public function testReplace(){
		$prefixKEY = "testReplace:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
		$con->remove($prefixKEY . $this->KEY);
		$this->assertFalse($con->replace($prefixKEY . $this->KEY, $this->VALUE, 0, 0));
	}
	
	public function testReplaceSuccess(){
		$prefixKEY = "testReplaceSuccess:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
	
		$con->put($prefixKEY . $this->KEY, $this->VALUE, 0, 0);
		$this->assertEquals($this->VALUE, $con->get($prefixKEY . $this->KEY));
		$this->assertTrue($con->replace($prefixKEY . $this->KEY, $this->VALUE2, 0, 0));
		$this->assertEquals($this->VALUE2, $con->get($prefixKEY . $this->KEY));
	}
	
	public function testReplaceExact(){
		$prefixKEY = "testReplaceSuccess:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
		try{
			$con->remove($prefixKEY);
			$this->assertFalse($con->replaceValue(
				$prefixKEY . $this->KEY, 
			$this->VALUE, 
			$this->VALUE2, function($a, $b){
				return strcmp($a,$b);
			},0, 0));
			$this->fail("expected error 1009");
		}
		catch(CacheException $e){
			if($e->getCode() != 1009){
				$this->fail("expected error 1009");
			}
		}
	}
	
	public function testReplaceExactSuccess(){
		$prefixKEY = "testReplaceExactSuccess:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
	
		try{
			$con->replaceValue(
				$prefixKEY . $this->KEY, 
				$this->VALUE, 
				$this->VALUE2, 
				function($a, $b){
					return strcmp($a,$b);
				}, 0, 0);
				
			$this->fail("expected error 1009");
		}
		catch(CacheException $e){
			if($e->getCode() != 1009){
				$this->fail("expected error 1009");
			}
		}
	}
	
	/* putIfAbsent */
	
	public function testputIfAbsent(){
		$prefixKEY = "testputIfAbsent:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
	
		try{
			$con->putIfAbsent($prefixKEY . $this->KEY, $this->VALUE, 0, 0);
			$this->fail("expected error 1009");
		}
		catch(CacheException $e){
			if($e->getCode() != 1009){
				$this->fail("expected error 1009");
			}
		}
	}
	
	public function testputIfAbsentExistValue(){
		$prefixKEY = "testputIfAbsentExistValue:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
	
		try{
			$this->assertEquals($this->VALUE, $con->putIfAbsent($prefixKEY . $this->KEY, $this->VALUE2, 0, 0));
			$this->fail("expected error 1009");
		}
		catch(CacheException $e){
			if($e->getCode() != 1009){
				$this->fail("expected error 1009");
			}
		}
	}
	
	/* put */
	
	public function testPut(){
		$prefixKEY = "testPut:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
	
		$con->remove($prefixKEY . $this->KEY);
		$this->assertNull($con->get($prefixKEY . $this->KEY));
		$con->put($prefixKEY . $this->KEY, $this->VALUE, 0, 0);
		$this->assertEquals($this->VALUE, $con->get($prefixKEY . $this->KEY));
	}
	
	/* get */
	
	public function testGet(){
		$prefixKEY = "testGet:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
	
		$con->remove($prefixKEY . $this->KEY);
		$this->assertNull($con->get($prefixKEY . $this->KEY));
		$con->put($prefixKEY . $this->KEY, $this->VALUE, 0, 0);
		$this->assertEquals($this->VALUE, $con->get($prefixKEY . $this->KEY));
	}
	
	public function testGetOverride(){
		$prefixKEY = "testGetOverride:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
	
		$con->remove($prefixKEY . $this->KEY);
		$this->assertNull($con->get($prefixKEY . $this->KEY));
		$con->put($prefixKEY . $this->KEY, $this->VALUE, 0, 0);
		$this->assertEquals($this->VALUE, $con->get($prefixKEY . $this->KEY));
		$con->put($prefixKEY . $this->KEY, $this->VALUE2, 0, 0);
		$this->assertEquals($this->VALUE2, $con->get($prefixKEY . $this->KEY));
	}
	
	/* remove */
	
	public function testRemoveExact(){
		$prefixKEY = "testRemoveExact:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
	
		try{
			$this->assertFalse($con->removeValue(
				$prefixKEY . $this->KEY, 
				$this->VALUE,
				function($a, $b){
					return strcmp($a,$b);
				}));
			$this->fail("expected error 1009");
		}
		catch(CacheException $e){
			if($e->getCode() != 1009){
				$this->fail("expected error 1009");
			}
		}
	
	}
	
	public function testRemove(){
		$prefixKEY = "testRemove:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
	
		$this->assertNull($con->get($prefixKEY . $this->KEY));
		$this->assertFalse($con->remove($prefixKEY . $this->KEY));
	
		$con->put($prefixKEY . $this->KEY, $this->VALUE, 0, 0);
	
		$this->assertEquals($this->VALUE, $con->get($prefixKEY . $this->KEY));
	
		$this->assertTrue($con->remove($prefixKEY . $this->KEY));
	}
	
	/* timeToLive */
	
	public function testTimeToLive(){
		$prefixKEY = "testTimeToLive:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
	
		$con->put($prefixKEY . $this->KEY, $this->VALUE, 1000, 0);
		$this->assertEquals($con->get($prefixKEY . $this->KEY), $this->VALUE);
		usleep(800000);
		$this->assertEquals($con->get($prefixKEY . $this->KEY), $this->VALUE);
		usleep(400000);
		$this->assertNull($con->get($prefixKEY . $this->KEY));
	}
	
	public function testTimeToLiveLessThanTimeToIdle(){
		$prefixKEY = "testTimeToLiveLessThanTimeToIdle:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
	
		$con->put($prefixKEY . $this->KEY, $this->VALUE, 1000, 5000);
		$this->assertEquals($con->get($prefixKEY . $this->KEY), $this->VALUE);
		usleep(1200000);
		$this->assertNull($con->get($prefixKEY . $this->KEY));
	}
	
	public function testNegativeTimeToLive(){
		$prefixKEY = "testNegativeTimeToLive:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
	
		try{
			$con->put($prefixKEY . $this->KEY, $this->VALUE, -1, 5000);
			$this->fail("expected timeToLive is invalid!");
		}
		catch(CacheException $e){
			if($e->getCode() != 1004 || strcmp($e->getMessage(),"Bad command syntax error!") != 0){
				$this->fail();
			}
		}
	
	}
	
	/* TimeToIdle */
	
	public function testTimeToIdle(){
		$prefixKEY = "testTimeToIdle:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
	
		$con->put($prefixKEY . $this->KEY, $this->VALUE, 0, 1000);
		$this->assertEquals($con->get($prefixKEY . $this->KEY), $this->VALUE);
		usleep(800000);
		$this->assertEquals($con->get($prefixKEY . $this->KEY), $this->VALUE);
		usleep(800000);
		$this->assertEquals($con->get($prefixKEY . $this->KEY), $this->VALUE);
		usleep(1200000);
		$this->assertNull($con->get($prefixKEY . $this->KEY));
	
	}
	
	public function testTimeToIdleLessThanTimeToLive(){
		$prefixKEY = "testTimeToIdleLessThanTimeToLive:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
	
		$con->put($prefixKEY . $this->KEY, $this->VALUE, 20000, 1000);
		$this->assertEquals($con->get($prefixKEY . $this->KEY), $this->VALUE);
		usleep(800000);
		$this->assertEquals($con->get($prefixKEY . $this->KEY), $this->VALUE);
		usleep(800000);
		$this->assertEquals($con->get($prefixKEY . $this->KEY), $this->VALUE);
		usleep(1200000);
		$this->assertNull($con->get($prefixKEY . $this->KEY));
	}
	
	public function testNegativeTimeToIdle(){
		$prefixKEY = "testNegativeTimeToIdle:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT, false);
	
		try{
			$con->put($prefixKEY . $this->KEY, $this->VALUE, 0, -1);
			$this->fail("expected timeToIdle is invalid!");
		}
		catch(CacheException $e){
			if($e->getCode() != 1004 || strcmp($e->getMessage(),"Bad command syntax error!") != 0){
				$this->fail();
			}
		}
	}

}
