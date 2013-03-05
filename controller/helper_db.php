<?
	define("SERVER", "localhost");
	define("USER", "srv_app");
	define("PASS", "");
	define("DB",  "db_todo");

	global $conn;
	
	if(!$conn = Connect()) 
		die("err db"); 
	else 
		$conn->query("SET NAMES 'utf8';");
	
	//Establish Database Connection and returns the Connection Object
	function Connect()
	{			
		$con = mysqli_connect(SERVER,USER,PASS, DB);
		if (!$con)
			die("Could not connect to Server: $SERVER, Database: $DB, Error:".mysqli_connect_error());					
		
		return $con;	   
	}  
	
	function ExecuteQuery($pSql)
	{
		global $conn;	
		$res =  mysqli_query($conn, $pSql) or die("Could not execute Query: $pSql, Error ".mysqli_error($conn	));						
		return $res;
	}
	
	function ExecuteNonQuery($pSql)
	{
		global $conn;				
		mysqli_query($conn, $pSql) or die("Could not execute Query: $pSql");
		return mysqli_affected_rows($conn);
	}		
		
	function ExecuteQueryAsArray($pSql)
	{
		global $conn;	
		$res = ExecuteQuery($pSql);
		$newArray = array();
	   
		while ($row = mysqli_fetch_array($res))               			
			$newArray[] = $row;			
				
		//Important to fix commands out of sync error		
		mysqli_next_result($conn);
		return $newArray;									   
	}
	
	function ExecuteQueryAsHTMLTable($pSql)
	{
		$htmlTable = "<table border=1>%s</table>";
				
		$rs = ExecuteQuery($pSql);		
		
		$out = "";
		while ($field = $rs->fetch_field()) 
			$out .= "<th>".$field->name."</th>";
		
		while ($linea = $rs->fetch_assoc()) 
		{
			$out .= "<tr>";
			foreach ($linea as $valor_col) 
				$out .= '<td>'.$valor_col.'</td>';
			$out .= "</tr>";
		}
				
		return sprintf($htmlTable, $out);		
	}
           
	/*
	{
		"beverages": "Coffee,Coke",
		"snacks": 	 "Chips,Cookies"
	}		
	*/	
	function ExecuteQueryAsJSON($pSql, $pGroupBy = '')
	{
		$arr = ExecuteQueryAsArray($pSql);
		$NAME = "name";
		
		if (empty($pGroupBy))		
			return json_encode($arr);		
		else
		{
			$temp = "";
			$lastId = null;
			
			foreach($arr as $i)
			{			
				$id = $i[$pGroupBy];
				
				if (empty($lastId)){
					$lastId = $id;
					$temp.= '"groupby": "'.$id.'", "data": ['.json_encode($i);
					continue;
				}
				
				if ($lastId == $id){
					$temp.= ','.json_encode($i);
				}
				else {													
					$temp.= ']}, { "groupby": "'.$id.'",  "data": ['.json_encode($i);
					$lastId = $id;
				}
			}
			
			$temp.= ']';
			
			echo '[ {  '.$temp.'			} ]  ';
		}
	}		   

	
?>

