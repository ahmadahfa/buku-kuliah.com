<?php
	//$link = new mysqli("localhost", "root", "", "buku-kuliah");
	$link = new mysqli("mysql16.000webhost.com", "a6873773_c4", "cemp4t", "a6873773_c4");
	$temp = $link->query("SELECT `Username`, `Nama` FROM `aktor_sistem` WHERE 1");
	$return = $users = array();
	
	
	while($line = $temp->fetch_assoc()){
		$username = $line['Username'];
		$name = $line['Nama'];
		$users[$username] = $name;
	}
	
	$term = $_GET["term"];
	foreach($users as $k=>$v){
		if(preg_match("/$term/i", $k)) { $return[] = $k; } 
		if(preg_match("/$term/i", $v)) { $return[] = $k; } 
	} 
	 echo $_GET['callback'] . '(' . json_encode(array_values(array_unique($return))) . ');'; 
	 
	 $link->close();
?>