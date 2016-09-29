<?php
class CacheTest{
	
	public function test1(){
		$con = new BRCacheConnection();
		$con->replace("key", "value");
	}
}