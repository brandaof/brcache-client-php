<?php
class BRCacheReceiver{
	
	function __construct(){
	}
	
	public function processPutResult($con){
		
		$resp = $this->readLine($con);
		
		switch ($resp[0]) {
			case 's':
				return false;
			case 'r':
				return true;
			default:
				$error = $this->parseError($resp);
				throw new Exception($error->message, $error->code);
		}
				
	}

	public function processReplaceResult($con){
	
		$resp = $this->readLine($con);
	
		switch ($resp[0]) {
			case 'r':
				return true;
			case 'n':
				return false;
			default:
				$error = $this->parseError($resp);
				throw new Exception($error->message, $error->code);
		}
	
	}

	public function processGetResult($con){
	
		$resp = $this->readLine($con);
	
		if($resp[0] == 'v'){
			$entry = $this->getObject($con, $resp);
			
			$boundary = $this->readLine($con);
				
			if(!strcmp($boundary, BRCacheConnection::$BOUNDARY)){
				throw new Exception("expected end");
			}
				
			return $entry == null? null : unserialize($entry->dta);
		}
		else{
			$error = $this->parseError($resp);
			throw new Exception($error->message, $error->code);
		}
	
	}

	public function processMultiGetResult($con){
	
		$resp   = $this->readLine($con);
		$result = Array();
		
		while($resp[0] == 'v'){
			$entry = $this->getObject($con, $resp);
			
			if($entry != null){
				$resul[$key] = unserialize($entry->dta);
			}
			
			$resp   = $this->readLine($con);
		}
		
		if(!strcmp($resp, BRCacheConnection::$BOUNDARY)){
			$error = $this->parseError($resp);
			throw new Exception($error->message, $error->code);
		}
		
		return $result;
	}
	
	public function processRemoveResult($con){
	
		$resp = $this->readLine($con);
	
		switch ($resp[0]) {
			case 'o':
				return true;
			case 'n':
				return false;
			default:
				$error = $this->parseError($resp);
				throw new Exception($error->message, $error->code);
		}
	
	}

	public function processSetResult($con){
	
		$resp = $this->readLine($con);
	
		switch ($resp[0]) {
			case 's':
				return true;
			case 'n':
				return false;
			default:
				$error = $this->parseError($resp);
				throw new Exception($error->message, $error->code);
		}
	
	}

	public function processBeginTransactionResult(){
		$this->processDefaultTransactionCommandResult();
	}
	
	public function processCommitTransactionResult(){
		$this->processDefaultTransactionCommandResult();
	}
	
	public function processRollbackTransactionResult(){
		$this->processDefaultTransactionCommandResult();
	}
	
	public function processDefaultTransactionCommandResult($con){
	
		$resp = $this->readLine($con);
	
		if($resp[0] != 'o'){
			$error = $this->parseError($resp);
			throw new Exception($error->message, $error->code);
		}
		
	}

	public function processShowVarResult($con, $var){
	
		$resp = $this->readLine($con);
	
		$expectedPrefix = $var . ": ";
		$prefixLen = strlen($expectedPrefix);
		
		$prefix = substr($resp, 0, strlen($expectedPrefix));
		
		if(strcmp($expectedPrefix, $prefix)){
			$value = substr($resp, strlen($expectedPrefix), strlen($resp));
			return $value;
		}
		else{
			$error = $this->parseError($resp);
			throw new Exception($error->message, $error->code);
		}
	
	}

	public function processSetVarResult($con){
	
		$resp = $this->readLine($con);
	
		if($resp[0] != 'o'){
			$error = $this->parseError($resp);
			throw new Exception($error->message, $error->code);
		}
			
	}
	
	private function getObject($con, $header){
		
		$params = explode(BRCacheConnection::$SEPARATOR_COMMAND, $header);
		
		$key   = $params[1];
		$size  = intval($params[2]);
		$flags = intval($params[3]);
		
		if($size > 0){
			$buf = fread($con, $size + 2);
			//$end = fread($con, 2);
			
			//if(!strcmp($end, BRCacheConnection::$CRLF)){
			//	throw new Exception("corrupted data: " . $key);
			//}
			
			$entry = new stdClass();
			$entry->key   = $key;
			$entry->size  = $size;
			$entry->flags = $flags;
			$entry->dta   = $buf;
			return $entry;
		}
		else{
			return null;
		}
	}
	
	private function readLine($con){
		return fgets($con);
	}
	
	private function parseError($resp){
		$code    = substr($resp, 6, 10);
		$message = substr($resp, 12, strlen($resp));
		
		$error = new stdClass();
		$error->code    = $code;
		$error->message = $message;
		return $error;
	}
	
}