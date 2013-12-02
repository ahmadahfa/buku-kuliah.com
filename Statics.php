<?php
	include_once("dam.php");
	
	class Statics{
		private $db;		
		public function __construct(){
			$this->db = new DAM();
		}
		
		
		public function getAllCategories(){
			$temp = $this->db->retrieve("kategori", true);
			$result = array();
			while($line = $temp->fetch_assoc())
				$result[] = $line;
			return $result;
		}
	}
	
	
?>