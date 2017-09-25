<?php
	$db = new mysqli('localhost','root','1234','senatedb');

	if ($db->connect_error) {
	    die("Connection failed: " . $db->connect_error);
	} 

	function insert($number , $title, $filed, $db ){
		//BILL NUMBER
		$number = str_replace("Senate Bill No. ", "", $number);
		echo $number."</br>";

		$filed = str_replace("Filed on ", "", $filed);
		$dateAndNames = explode(" by ", $filed);

		//DATE FILED
		$date = $dateAndNames[0];	
		$date = date('Y-m-d',strtotime($date));
		echo $date."</br>";


		$names = $dateAndNames[1];
		$namesArr = explode(",", $names);
		
		//INSERT TO DB PER NAME
		for($ctr = 0 ; $ctr < sizeof($namesArr); $ctr+=2){
			$completeName = rtrim(ltrim($namesArr[$ctr+1])).' '.rtrim(ltrim($namesArr[$ctr]));			
			$sql = "INSERT INTO Bills
					 	 VALUES (0,{$number}, '{$date}', '{$completeName}','{$title}');";
			
			// //INSERT TO DB
			// if($db->real_query($sql) === TRUE ){
			// 	echo "Success! Data inserted."."<br/>";
			// }else{
			// 	echo "Failed!"."<br>" .$db->error;
			// }
			
			
		}
		
		echo "<hr/>";

		
	}

	function clean($nodes){
		$new = $nodes;

		for($ctr = 0; $ctr < $nodes->childNodes->length; $ctr++){
			$node = $nodes->childNodes->item($ctr);
			
			if($node->nodeType == 8 || ($node->nodeType == 3 && trim($node->wholeText) == '' )){				
				$new->removeChild($node);
			}
		}	 
		return $new;
	}





?>	