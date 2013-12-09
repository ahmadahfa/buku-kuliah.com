<?php
	require("Buku.php");
	include_once("dam.php");
	
	class BukuManager{
		private $db;		
		public function __construct(){
			$this->db = new DAM();
		}
		
		public function createBuku($data){
			$data = $this->validate($data);
			$b = new Buku($data);
			return $this->db->create("buku", $b);
		}
		
		public function validate($data){
			$retVal = array();
			foreach($data as $key=>$value){
				if(is_array($value)){
					foreach($value as $k=>$v){
						$retVal[$key][$k] = $this->db->real_escape_string($v);
					}
				}else
					$retVal[$key] = $this->db->real_escape_string($value);
			}
			return $retVal;
		}
		
		public function getBuku($data){
			//var_dump($data); exit(0);
			$data = $this->validate($data);
			return $this->db->retrieve("buku", $data);
		}
		
		public function getAllBuku(){
			//var_dump($data); exit(0);
			return $this->db->retrieve("buku", true);
		}
		
		public function getAllBukuByPengguna($data){
			//var_dump($data); exit(0);
			$data = $this->validate($data);
			return $this->db->retrieve("buku-pengguna", $data);
		}
				
		public function deleteBuku($data){
			//var_dump($data); exit(0);
			$data = $this->validate($data);
			return $this->db->delete("buku", $data);
		}
		
		public function addBuku($data){
			//var_dump($data); exit(0);
			$cats = $this->validate($data['Kategori']);
			unset($data['Kategori']);
			if($this->createBuku($data)){ // saving book data
				$bookId = $this->db->insert_id;
				$tags = $data["Tags"];
				$userId = $data["ID"];
				$review = $data["Resensi"];
				foreach($tags as $tag){
					$data = array("IDBuku"=>$bookId, "Tag"=>$tag);
					if(!$this->db->create("tag", $data)){
						throw new Exception("Saving tags data failed.");
					}
				}
				$time = date("Y-m-d H:i:s");
				$data = array("IDBuku"=>$bookId, "IDPemberi"=>$userId, "Isi_Resensi"=>$review, "Waktu_Resensi"=>$time);
				if($this->db->create("resensi", $data)){
					foreach($cats as $cat){
						if(!$this->db->create("memiliki_kategori", array("IDBuku"=>$bookId, "IDKategori"=>$cat))){
							throw new Exception("Saving categories data failed.");
						}
					}
				}else{
					throw new Exception("Saving review data failed.");
				}
			}else{
				throw new Exception("Saving book data failed.");
			}
		}
		
		public function addResensi($data){
			$data = $this->validate($data);
			return $this->db->create("resensi", $data);
		}
		
		public function getResensi($data){
			$data = $this->validate($data);
			return $this->db->retrieve("resensi", $data);
		}
		
		public function getKategori($data){
			$data = $this->validate($data);
			return $this->db->retrieve("kategori", $data);
		}
		
		public function editBuku($data, $cond){
			$cats = $this->validate($data['Kategori']);
			unset($data['Kategori']);
			$tags = $this->validate($data['Tags']);
			unset($data['Tags']);
			$Isi_Resensi = $data['Resensi'];
			unset($data['Resensi']);
			$IDResensi = $data['IDResensi'];
			unset($data['IDResensi']);
			
			$bookId = $cond['buku.IDBuku'];
			//var_dump($data);exit(0);
			$data = $this->validate($data);
			$cond = $this->validate($cond);
			if($this->db->update("buku", $data, $cond)){
				if($this->db->update("tag", $tags, array("IDBuku"=>$bookId))){
					if($this->db->update("memiliki_kategori", $cats, array("IDBuku"=>$bookId))){
						if($this->db->update("resensi", array("Isi_Resensi"=>$Isi_Resensi), array("IDResensi"=>$IDResensi))){
							return true;
						}else{
							throw new Exception("Fail to save book review.");
						}
					}else{
						throw new Exception("Fail to save book categories.");
					}
				}else{
					throw new Exception("Fail to save book tags.");
				}
			}else{
				throw new Exception("Fail to save book data.");
			}
		}
		
		public function search($data){
			$data = $this->validate($data);
			return $this->db->search($data);
		}
		
		public function rate($data){
			$bookId = $data['IDBuku'];
			$rating = $data['Rating'];
			$p = $_SESSION["SessionData"];
			$bookInfo = $this->getBuku(array("buku.IDBuku"=>$bookId));
			//var_dump($bookInfo);exit(0);
			$oldRaters = $bookInfo['info']['Jumlah_Rater'];
			$oldRating = $bookInfo['info']['Rating'];
			$newRating = ($rating+$oldRating*$oldRaters)/($oldRaters+1);
			$newRaters = $oldRaters + 1;
			
			$data = $this->validate(array('Rating'=>$newRating, 'Jumlah_Rater'=>$newRaters));
			$cond = $this->validate(array('IDBuku'=>$bookId));
			return $this->db->update("buku", $data, $cond) && $this->db->create("rater_buku", array("IDBuku"=>$bookId, "ID"=>$p['ID']));
		}
		
		public function hasRated($data){
			$data = $this->validate($data);
			return $this->db->retrieve("rater_buku", $data) !== false && $this->db->retrieve("rater_buku", $data)->num_rows > 0;
		}
	}
	
	
?>