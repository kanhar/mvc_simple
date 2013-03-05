  <?php

require_once ("controller/helper_db.php");
require_once ("controller/helper_fn.php");

class Rest{
				
	function login($user, $pass)
	{	
		$sql = "select *from tbl_user where username='$user' and password='$pass'";
		$arr = ExecuteQueryAsArray($sql);
		if (empty($arr))
			die("Invalid Login");
		else
		{			
			$this->start();	
			
			$token = create_guid();
				
			$_SESSION[$token]['name'] 	= $user;
			$_SESSION[$token]['ip'] 	= getClientIP();
			$_SESSION[$token]['time'] 	= date('m/d/Y h:i:s a', time());						
			
			echo json_encode($token);
		}
	}	
	
	function logout($session_id)
	{	
		$this->start();
		
		$_SESSION[$session_id] = null;
		
		echo json_encode("success");		
	}
	
	function secret($session_id)
	{
		$this->start();
		
		if (!isset($_SESSION[$session_id]) || empty($_SESSION[$session_id]))
			die("Invalid session. Your IP has been recorded");
		
		$name = $_SESSION[$session_id]['name'];
		
		$sql = "select *from tbl_secret s
				join tbl_user u on u.id = s.user_id
				where username='$name'";
		echo json_encode(ExecuteQueryAsArray($sql));		
	}
	
	function start()
	{
		if(!isset($_SESSION)){session_start();} 
	}
	
	function getsecrets()
	{
		$sql = "select *from tbl_secret";
		echo json_encode(ExecuteQueryAsArray($sql));
	}

	function role()
	{
		$sql = "select *from role";
		$arr = ExecuteQueryAsArray($sql);
		$newarr = array("data" => $arr);
		echo json_encode($newarr);
	}

	function load($pOffset, $pLimit, $pResource, $pSelectFields, $pWhereFields, $pOrderByFields, $pFormat)
	{		
		
		if (empty($pResource))
			die("Error: You need to specify a resource");
		else
			$resource = $pResource;		
			
		if (empty($pSelectFields ) || $pSelectFields == "null")
			$selectFields = "*";
		else
			$selectFields = $pSelectFields;
			
		if (empty($pWhereFields ) || $pWhereFields == "null")
			$whereField = "";
		else
			$whereField = "where $pWhereFields";

		if (empty($pOrderByFields ) || $pOrderByFields == "null")
			$orderByField = "";
		else
			$orderByField = "order by $pOrderByFields";
			
		$sql = "select $selectFields from $resource $whereField $orderByField LIMIT $pOffset, $pLimit ";
		
		if ($pFormat == 'html')
			echo ExecuteQueryAsHTMLTable($sql);
		else if ($pFormat == 'sql')
			echo $sql;
		else
			echo json_encode(ExecuteQueryAsArray($sql));		
	}
	
	function roletable()
	{
		$sql = "select *from role";
		echo ExecuteQueryAsHTMLTable($sql);
	}


	function furniture()
	{
		$sql = "select *from furniture";
		echo json_encode(ExecuteQueryAsArray($sql));
	}
	
	
	//Clear all session values: To be done once a week or once a day
	function purge()
	{
		session_unset();
	}
	
}
