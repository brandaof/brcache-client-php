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
		$this->assertFalse(con.replace(prefixKEY + $this->KEY, $this->VALUE, 0, 0));
	}
	
	public function testReplaceSuccess(){
		$prefixKEY = "testReplaceSuccess:";
		$con = new BrCacheConnectionImp($this->SERVER_HOST, SERVER_PORT);
	
		con.put(prefixKEY + KEY, VALUE, 0, 0);
		TestCase.assertEquals(VALUE, con.get(prefixKEY + KEY));
		TestCase.assertTrue(con.replace(prefixKEY + KEY, VALUE2, 0, 0));
		TestCase.assertEquals(VALUE2, con.get(prefixKEY + KEY));
	}
	
	public function testReplaceExact(){
		$prefixKEY = "testReplaceSuccess:";
		BrCacheConnection con = new BrCacheConnectionImp($this->SERVER_HOST, SERVER_PORT);
		try{
			TestCase.assertFalse(con.replace(prefixKEY + KEY, VALUE, VALUE2, 0, 0));
			fail("expected error 1009");
		}
		catch(CacheException e){
			if(e.getCode() != 1009){
				fail("expected error 1009");
			}
		}
	}
	
	public function testReplaceExactSuccess(){
		$prefixKEY = "testReplaceExactSuccess:";
		BrCacheConnection con = new BrCacheConnectionImp($this->SERVER_HOST, SERVER_PORT);
	
		try{
			con.replace(prefixKEY + KEY, VALUE, VALUE2, 0, 0);
			fail("expected error 1009");
		}
		catch(CacheException e){
			if(e.getCode() != 1009){
				fail("expected error 1009");
			}
		}
	}
	
	/* putIfAbsent */
	
	public function testputIfAbsent(){
		$prefixKEY = "testputIfAbsent:";
		BrCacheConnection con = new BrCacheConnectionImp($this->SERVER_HOST, SERVER_PORT);
	
		try{
			con.putIfAbsent(prefixKEY + KEY, VALUE, 0, 0);
			fail("expected error 1009");
		}
		catch(CacheException e){
			if(e.getCode() != 1009){
				fail("expected error 1009");
			}
		}
	}
	
	public function testputIfAbsentExistValue(){
		$prefixKEY = "testputIfAbsentExistValue:";
		BrCacheConnection con = new BrCacheConnectionImp($this->SERVER_HOST, SERVER_PORT);
	
		try{
			TestCase.assertEquals(VALUE, con.putIfAbsent(prefixKEY + KEY, VALUE2, 0, 0));
			fail("expected error 1009");
		}
		catch(CacheException e){
			if(e.getCode() != 1009){
				fail("expected error 1009");
			}
		}
	}
	
	/* put */
	
	public function testPut(){
		$prefixKEY = "testPut:";
		BrCacheConnection con = new BrCacheConnectionImp($this->SERVER_HOST, SERVER_PORT);
	
		TestCase.assertNull(con.get(prefixKEY + KEY));
		con.put(prefixKEY + KEY, VALUE, 0, 0);
		TestCase.assertEquals(VALUE, con.get(prefixKEY + KEY));
	}
	
	/* get */
	
	public function testGet(){
		$prefixKEY = "testGet:";
		BrCacheConnection con = new BrCacheConnectionImp($this->SERVER_HOST, SERVER_PORT);
	
		TestCase.assertNull(con.get(prefixKEY + KEY));
		con.put(prefixKEY + KEY, VALUE, 0, 0);
		TestCase.assertEquals(VALUE, con.get(prefixKEY + KEY));
	}
	
	public function testGetOverride(){
		$prefixKEY = "testGetOverride:";
		BrCacheConnection con = new BrCacheConnectionImp($this->SERVER_HOST, SERVER_PORT);
	
		TestCase.assertNull(con.get(prefixKEY + KEY));
		con.put(prefixKEY + KEY, VALUE, 0, 0);
		TestCase.assertEquals(VALUE, con.get(prefixKEY + KEY));
		con.put(prefixKEY + KEY, VALUE2, 0, 0);
		TestCase.assertEquals(VALUE2, con.get(prefixKEY + KEY));
	}
	
	/* remove */
	
	public function testRemoveExact(){
		$prefixKEY = "testRemoveExact:";
		BrCacheConnection con = new BrCacheConnectionImp($this->SERVER_HOST, SERVER_PORT);
	
		try{
			TestCase.assertFalse(con.remove(prefixKEY + KEY, VALUE));
			fail("expected error 1009");
		}
		catch(CacheException e){
			if(e.getCode() != 1009){
				fail("expected error 1009");
			}
		}
	
	}
	
	public function testRemove(){
		$prefixKEY = "testRemove:";
		BrCacheConnection con = new BrCacheConnectionImp($this->SERVER_HOST, SERVER_PORT);
	
		TestCase.assertNull((String)con.get(prefixKEY + KEY));
		TestCase.assertFalse(con.remove(prefixKEY + KEY));
	
		con.put(prefixKEY + KEY, VALUE, 0, 0);
	
		TestCase.assertEquals(VALUE, (String)con.get(prefixKEY + KEY));
	
		TestCase.assertTrue(con.remove(prefixKEY + KEY));
	}
	
	/* timeToLive */
	
	public function testTimeToLive(){
		$prefixKEY = "testTimeToLive:";
		BrCacheConnection con = new BrCacheConnectionImp($this->SERVER_HOST, SERVER_PORT);
	
		con.put(prefixKEY + KEY, VALUE, 1000, 0);
		assertEquals(con.get(prefixKEY + KEY), VALUE);
		Thread.sleep(800);
		assertEquals(con.get(prefixKEY + KEY), VALUE);
		Thread.sleep(400);
		assertNull(con.get(prefixKEY + KEY));
	}
	
	public function testTimeToLiveLessThanTimeToIdle(){
		$prefixKEY = "testTimeToLiveLessThanTimeToIdle:";
		BrCacheConnection con = new BrCacheConnectionImp($this->SERVER_HOST, SERVER_PORT);
	
		con.put(prefixKEY + KEY, VALUE, 1000, 5000);
		assertEquals(con.get(prefixKEY + KEY), VALUE);
		Thread.sleep(1200);
		assertNull(con.get(prefixKEY + KEY));
	}
	
	public function testNegativeTimeToLive(){
		$prefixKEY = "testNegativeTimeToLive:";
		BrCacheConnection con = new BrCacheConnectionImp($this->SERVER_HOST, SERVER_PORT);
	
		try{
			con.put(prefixKEY + KEY, VALUE, -1, 5000);
			fail("expected timeToLive is invalid!");
		}
		catch(CacheException e){
			if(e.getCode() != 1004 || !e.getMessage().equals("Bad command syntax error!")){
				fail();
			}
		}
	
	}
	
	/* TimeToIdle */
	
	public function testTimeToIdle(){
		$prefixKEY = "testTimeToIdle:";
		BrCacheConnection con = new BrCacheConnectionImp($this->SERVER_HOST, SERVER_PORT);
	
		con.put(prefixKEY + KEY, VALUE, 0, 1000);
		assertEquals(con.get(prefixKEY + KEY), VALUE);
		Thread.sleep(800);
		assertEquals(con.get(prefixKEY + KEY), VALUE);
		Thread.sleep(800);
		assertEquals(con.get(prefixKEY + KEY), VALUE);
		Thread.sleep(1200);
		assertNull(con.get(prefixKEY + KEY));
	
	}
	
	public function testTimeToIdleLessThanTimeToLive(){
		$prefixKEY = "testTimeToIdleLessThanTimeToLive:";
		BrCacheConnection con = new BrCacheConnectionImp($this->SERVER_HOST, SERVER_PORT);
	
		con.put(prefixKEY + KEY, VALUE, 20000, 1000);
		assertEquals(con.get(prefixKEY + KEY), VALUE);
		Thread.sleep(800);
		assertEquals(con.get(prefixKEY + KEY), VALUE);
		Thread.sleep(800);
		assertEquals(con.get(prefixKEY + KEY), VALUE);
		Thread.sleep(1200);
		assertNull(con.get(prefixKEY + KEY));
	}
	
	public function testNegativeTimeToIdle(){
		$prefixKEY = "testNegativeTimeToIdle:";
		BrCacheConnection con = new BrCacheConnectionImp($this->SERVER_HOST, SERVER_PORT);
	
		try{
			con.put(prefixKEY + KEY, VALUE, 0, -1);
			fail("expected timeToIdle is invalid!");
		}
		catch(CacheException e){
			if(e.getCode() != 1004 || !e.getMessage().equals("Bad command syntax error!")){
				fail();
			}
		}
	}
	
}