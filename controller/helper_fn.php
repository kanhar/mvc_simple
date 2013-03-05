<?
	function create_guid($namespace = '') 
	{     
		$guid = '';
		$uid = uniqid("", true);
		$data = $namespace;
		$data .= $_SERVER['REQUEST_TIME'];
		$data .= $_SERVER['HTTP_USER_AGENT'];		
		$data .= $_SERVER['REMOTE_ADDR'];
		$data .= $_SERVER['REMOTE_PORT'];
		$hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
		$guid = '{' .   
				substr($hash,  0,  8) . 
				'-' .
				substr($hash,  8,  4) .
				'-' .
				substr($hash, 12,  4) .
				'-' .
				substr($hash, 16,  4) .
				'-' .
				substr($hash, 20, 12) .
				'}';
		return $guid;
	}	
	
	function getClientIP()
	{
		if ( isset($_SERVER["REMOTE_ADDR"]) )
			$ip = $_SERVER["REMOTE_ADDR"];
		else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) )    
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"] ;
		else if ( isset($_SERVER["HTTP_CLIENT_IP"]) )     
			$ip = $_SERVER["HTTP_CLIENT_IP"] . ' '; 
		else
			$ip = 'Unknown';
			
		return $ip;
	}
?>