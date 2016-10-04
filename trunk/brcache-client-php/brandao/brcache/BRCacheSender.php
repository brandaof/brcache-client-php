<?php
require_once 'BRCacheConnection.php';

class BRCacheSender{
	
	function __construct(){
	}
	
	public function put($con, $key, $value, $timeToLive, $timeToIdle){
		
		$data = serialize($value);
		
		$header =
			BRCacheConnection::$PUT_COMMAND . BRCacheConnection::$SEPARATOR_COMMAND .
			$key . BRCacheConnection::$SEPARATOR_COMMAND . 
			$timeToLive . BRCacheConnection::$SEPARATOR_COMMAND .
			$timeToIdle . BRCacheConnection::$SEPARATOR_COMMAND .
			strlen($data) . BRCacheConnection::$SEPARATOR_COMMAND .
			BRCacheConnection::$DEFAULT_FLAGS . BRCacheConnection::$CRLF;
		
		fwrite($con, $header);
		fwrite($con, $data);
		fwrite($con, BRCacheConnection::$CRLF);
	}

	public function replace($con, $key, $value, $timeToLive, $timeToIdle){
	
		$data = serialize($value);
	
		$header =
			BRCacheConnection::$REPLACE_COMMAND . BRCacheConnection::$SEPARATOR_COMMAND .
			$key . BRCacheConnection::$SEPARATOR_COMMAND .
			$timeToLive . BRCacheConnection::$SEPARATOR_COMMAND .
			$timeToIdle . BRCacheConnection::$SEPARATOR_COMMAND .
			strlen($data) . BRCacheConnection::$SEPARATOR_COMMAND .
			BRCacheConnection::$DEFAULT_FLAGS . BRCacheConnection::$CRLF;
			
		fwrite($con, $header);
		fwrite($con, $data);
		fwrite($con, BRCacheConnection::$CRLF);
	}

	public function set($con, $key, $value, $timeToLive, $timeToIdle){
	
		$data = serialize($value);
	
		$header =
			BRCacheConnection::$SET_COMMAND . BRCacheConnection::$SEPARATOR_COMMAND .
			$key . BRCacheConnection::$SEPARATOR_COMMAND .
			$timeToLive . BRCacheConnection::$SEPARATOR_COMMAND .
			$timeToIdle . BRCacheConnection::$SEPARATOR_COMMAND .
			strlen($data) . BRCacheConnection::$SEPARATOR_COMMAND .
			BRCacheConnection::$DEFAULT_FLAGS . BRCacheConnection::$CRLF;
			
		fwrite($con, $header);
		fwrite($con, $data);
		fwrite($con, BRCacheConnection::$CRLF);
	}

	public function get($con, $key, $forUpdate){
	
		$header =
			BRCacheConnection::$GET_COMMAND . BRCacheConnection::$SEPARATOR_COMMAND .
			$key . BRCacheConnection::$SEPARATOR_COMMAND .
			($forUpdate? "1" : "0") . BRCacheConnection::$SEPARATOR_COMMAND .
			BRCacheConnection::$DEFAULT_FLAGS . BRCacheConnection::$CRLF;
			
		fwrite($con, $header);
	}

	public function remove($con, $key){
	
		$header =
			BRCacheConnection::$REMOVE_COMMAND . BRCacheConnection::$SEPARATOR_COMMAND .
			$key . BRCacheConnection::$SEPARATOR_COMMAND .
			BRCacheConnection::$DEFAULT_FLAGS . BRCacheConnection::$CRLF;
			
		fwrite($con, $header);
	}

	public function beginTransaction($con){
	
		$header =
			BRCacheConnection::$BEGIN_TX_COMMAND . BRCacheConnection::$CRLF;
			
		fwrite($con, $header);
	}

	public function commitTransaction($con){
	
		$header =
			BRCacheConnection::$COMMIT_TX_COMMAND . BRCacheConnection::$CRLF;
			
		fwrite($con, $header);
	}

	public function rollbackTransaction($con){
	
		$header =
			BRCacheConnection::$ROLLBACK_TX_COMMAND . BRCacheConnection::$CRLF;
			
		fwrite($con, $header);
	}

	public function showVar($con, $var){
	
		$header =
			BRCacheConnection::$SHOW_VAR . BRCacheConnection::$SEPARATOR_COMMAND .
			$var . BRCacheConnection::$CRLF;
		
		fwrite($con, $header);
	}

	public function showVars($con){
	
		$header = BRCacheConnection::$SHOW_VARS . BRCacheConnection::$CRLF;
		fwrite($con, $header);
	}
	
	public function setVar($con, $var, $value){
	
		$header =
			BRCacheConnection::$SET_VAR . BRCacheConnection::$SEPARATOR_COMMAND .
			$var . BRCacheConnection::$SEPARATOR_COMMAND .
			$value . BRCacheConnection::$CRLF;
		
		fwrite($con, $header);
	}
	
}