<?php
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase{
	
	private $SERVER_HOST	= "localhost";
	
	private $SERVER_PORT	= 1044;
	
	private $KEY			= "teste";
	
	private $VALUE			= "value";
	
	private $VALUE2			= "val";
	
	/* replace */
	
	public function testReplace(){
		$prefixKEY = "testReplace:";
		$con = new BrCacheConnection($this->SERVER_HOST, $this->SERVER_PORT);
		$this->assertFalse($con->replace($prefixKEY + $this->KEY, $this->VALUE, 0, 0));
	}
	
	public function testReplaceSuccess(){
		$prefixKEY = "testReplaceSuccess:";
		$con = new BrCacheConnectionImp($this->SERVER_HOST, $this->SERVER_PORT);
	
		$con->put($prefixKEY + $this->KEY, $this->VALUE, 0, 0);
		$this->assertEquals($this->VALUE, $con->get($prefixKEY + $this->KEY));
		$this->assertTrue($con->replace($prefixKEY + $this->KEY, $this->VALUE2, 0, 0));
		$this->assertEquals($this->VALUE2, $con->get($prefixKEY + $this->KEY));
	}
	
	public function testReplaceExact(){
		$prefixKEY = "testReplaceSuccess:";
		$con = new BrCacheConnectionImp($this->SERVER_HOST, $this->SERVER_PORT);
		try{
			$this->assertFalse($con->replace($prefixKEY + $this->KEY, $this->VALUE, $this->VALUE2, 0, 0));
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
		$con = new BrCacheConnectionImp($this->SERVER_HOST, $this->SERVER_PORT);
	
		try{
			$con->replace($prefixKEY + $this->KEY, $this->VALUE, $this->VALUE2, 0, 0);
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
		$con = new BrCacheConnectionImp($this->SERVER_HOST, $this->SERVER_PORT);
	
		try{
			$con->putIfAbsent($prefixKEY + $this->KEY, $this->VALUE, 0, 0);
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
		$con = new BrCacheConnectionImp($this->SERVER_HOST, $this->SERVER_PORT);
	
		try{
			$this->assertEquals($this->VALUE, $con->putIfAbsent($prefixKEY + $this->KEY, $this->VALUE2, 0, 0));
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
		$con = new BrCacheConnectionImp($this->SERVER_HOST, $this->SERVER_PORT);
	
		$this->assertNull($con->get($prefixKEY + $this->KEY));
		$con->put($prefixKEY + $this->KEY, $this->VALUE, 0, 0);
		$this->assertEquals($this->VALUE, $con->get($prefixKEY + $this->KEY));
	}
	
	/* get */
	
	public function testGet(){
		$prefixKEY = "testGet:";
		$con = new BrCacheConnectionImp($this->SERVER_HOST, $this->SERVER_PORT);
	
		$this->assertNull($con->get($prefixKEY + $this->KEY));
		$con->put($prefixKEY + $this->KEY, $this->VALUE, 0, 0);
		$this->assertEquals($this->VALUE, $con->get($prefixKEY + $this->KEY));
	}
	
	public function testGetOverride(){
		$prefixKEY = "testGetOverride:";
		$con = new BrCacheConnectionImp($this->SERVER_HOST, $this->SERVER_PORT);
	
		$this->assertNull($con->get($prefixKEY + $this->KEY));
		$con->put($prefixKEY + $this->KEY, $this->VALUE, 0, 0);
		$this->assertEquals($this->VALUE, $con->get($prefixKEY + $this->KEY));
		$con->put($prefixKEY + $this->KEY, $this->VALUE2, 0, 0);
		$this->assertEquals($this->VALUE2, $con->get($prefixKEY + $this->KEY));
	}
	
	/* remove */
	
	public function testRemoveExact(){
		$prefixKEY = "testRemoveExact:";
		$con = new BrCacheConnectionImp($this->SERVER_HOST, $this->SERVER_PORT);
	
		try{
			$this->assertFalse($con->remove($prefixKEY + $this->KEY, $this->VALUE));
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
		$con = new BrCacheConnectionImp($this->SERVER_HOST, $this->SERVER_PORT);
	
		$this->assertNull((String)$con->get($prefixKEY + $this->KEY));
		$this->assertFalse($con->remove($prefixKEY + $this->KEY));
	
		$con->put($prefixKEY + $this->KEY, $this->VALUE, 0, 0);
	
		$this->assertEquals($this->VALUE, (String)$con->get($prefixKEY + $this->KEY));
	
		$this->assertTrue($con->remove($prefixKEY + $this->KEY));
	}
	
	/* timeToLive */
	
	public function testTimeToLive(){
		$prefixKEY = "testTimeToLive:";
		$con = new BrCacheConnectionImp($this->SERVER_HOST, $this->SERVER_PORT);
	
		$con->put($prefixKEY + $this->KEY, $this->VALUE, 1000, 0);
		assertEquals($con->get($prefixKEY + $this->KEY), $this->VALUE);
		usleep(800000);
		assertEquals($con->get($prefixKEY + $this->KEY), $this->VALUE);
		usleep(400000);
		assertNull($con->get($prefixKEY + $this->KEY));
	}
	
	public function testTimeToLiveLessThanTimeToIdle(){
		$prefixKEY = "testTimeToLiveLessThanTimeToIdle:";
		$con = new BrCacheConnectionImp($this->SERVER_HOST, $this->SERVER_PORT);
	
		$con->put($prefixKEY + $this->KEY, $this->VALUE, 1000, 5000);
		assertEquals($con->get($prefixKEY + $this->KEY), $this->VALUE);
		usleep(1200000);
		assertNull($con->get($prefixKEY + $this->KEY));
	}
	
	public function testNegativeTimeToLive(){
		$prefixKEY = "testNegativeTimeToLive:";
		$con = new BrCacheConnectionImp($this->SERVER_HOST, $this->SERVER_PORT);
	
		try{
			$con->put($prefixKEY + $this->KEY, $this->VALUE, -1, 5000);
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
		$con = new BrCacheConnectionImp($this->SERVER_HOST, $this->SERVER_PORT);
	
		$con->put($prefixKEY + $this->KEY, $this->VALUE, 0, 1000);
		assertEquals($con->get($prefixKEY + $this->KEY), $this->VALUE);
		usleep(800000);
		assertEquals($con->get($prefixKEY + $this->KEY), $this->VALUE);
		usleep(800000);
		assertEquals($con->get($prefixKEY + $this->KEY), $this->VALUE);
		usleep(1200000);
		assertNull($con->get($prefixKEY + $this->KEY));
	
	}
	
	public function testTimeToIdleLessThanTimeToLive(){
		$prefixKEY = "testTimeToIdleLessThanTimeToLive:";
		$con = new BrCacheConnectionImp($this->SERVER_HOST, $this->SERVER_PORT);
	
		$con->put($prefixKEY + $this->KEY, $this->VALUE, 20000, 1000);
		assertEquals($con->get($prefixKEY + $this->KEY), $this->VALUE);
		usleep(800000);
		assertEquals($con->get($prefixKEY + $this->KEY), $this->VALUE);
		usleep(800000);
		assertEquals($con->get($prefixKEY + $this->KEY), $this->VALUE);
		usleep(1200000);
		assertNull($con->get($prefixKEY + $this->KEY));
	}
	
	public function testNegativeTimeToIdle(){
		$prefixKEY = "testNegativeTimeToIdle:";
		$con = new BrCacheConnectionImp($this->SERVER_HOST, $this->SERVER_PORT);
	
		try{
			$con->put($prefixKEY + $this->KEY, $this->VALUE, 0, -1);
			$this->fail("expected timeToIdle is invalid!");
		}
		catch(CacheException $e){
			if($e->getCode() != 1004 || strcmp($e->getMessage(),"Bad command syntax error!") != 0){
				$this->fail();
			}
		}
	}
	
}