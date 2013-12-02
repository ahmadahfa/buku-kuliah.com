<?php
	require("Pesan.php");
	require("dam.php");
	class PesanManager{
		private $db;		
		public function __construct(){
			$this->db = new DAM();
		}
		
		public function createPesan($data){
			$data = $this->validate($data);
			$p = new Pesan($data);
			return $this->db->create("pesan", $p);
		}
		
		public function validate($data){
			$retVal = array();
			foreach($data as $key=>$value){
				$retVal[$key] = $this->db->real_escape_string($value);
			}
			return $retVal;
		}
		
		public function getPesan($idPesan){
			$cond = array("IDPesan"=>$idPesan);
			$cond = $this->validate($cond);
			$data = array("Status_Pesan"=>1);
			$this->db->update("pesan", $data, $cond);
			return $this->db->retrieve("pesan-pengguna", $cond);
		}
		
		public function getAllPesanByPengguna($userId){
			$data = array("IDPenerima"=>$userId);
			$data = $this->validate($data);
			return $this->db->retrieve("pesan-pengguna", $data);
		}
		
		public function kirimPesan($data){
			$data = $this->validate($data);
			$to = $data['to'];
			$temp = $this->db->retrieve("pengguna", array("Username"=>$to));
			if($temp === false || $temp->num_rows == 0){
				throw new Exception("Username you typed is not exists.");
			}
			$temp = $temp->fetch_assoc();
			$data["receiverId"] = $temp['ID'];
			unset($data['to']);
			//var_dump($data);exit(0);
			return $this->createPesan($data);
		}
	
	}
	
	
?>