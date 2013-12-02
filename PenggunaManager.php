<?php
	require("Pengguna.php");
	include_once("dam.php");
	class PenggunaManager{
		private $db;		
		public function __construct(){
			$this->db = new DAM();
		}
		
		public function createPengguna($data){
			$data = $this->validate($data);
			$p = new Pengguna($data);
			return $this->db->create("pengguna", $p);
		}
		
		public function validate($data){
			$retVal = array();
			foreach($data as $key=>$value){
				$retVal[$key] = $this->db->real_escape_string($value);
			}
			return $retVal;
		}
		public function authenticate($data){
			$data = $this->validate($data);
			$temp = $this->db->retrieve("pengguna", $data);
			//var_dump($authData);exit(0);
			
			if($temp->num_rows > 0){
				return array("status"=>0, "data"=>$temp);
			}else{
				$temp = $this->db->retrieve("pengguna", array("username"=>$data['username']));
				if($temp->num_rows>0)
					return array("status"=>2);
				else
					return array("status"=>1);
			}
		}
		
		public function getPengguna($data){
			$data = $this->validate($data);
			return $this->db->retrieve("pengguna", $data);
		}
		
		public function savePasswordRecoveryData($data){
			$data = $this->validate($data);
			return $this->db->create("reset_data", $data);
		}
		
		public function getPasswordRecoveryData($data){
			$data = $this->validate($data);
			$temp = $this->db->retrieve("reset_data", $data);
			if($temp){
				$hash = $data['Hash'];
				$data = array("Hash"=>$hash);
				$this->db->delete("reset_data", $data);
				return $temp;
			}
			return false;
		}
		
		public function editPengguna($data){
			$data = $this->validate($data);
			$cond = array('ID'=>$data['ID']);
			unset($data['ID']);
			// echo "<br />data: <br />";var_dump($data);echo "<br />cond: <br />";var_dump($cond);exit(0);
			return $this->db->update("pengguna", $data, $cond);
		}
		
		public function reset($data){
			$data = $this->validate($data);
			$cond = array('ID'=>$data['ID']);
			$data = array('Password'=>$data['Password']);
			return $this->db->update("pengguna", $data, $cond);
		}
		
		public function getAllPengguna(){
			return $this->db->retrieve("pengguna", true);
		}
	
	}
	
	
?>