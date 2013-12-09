<?php
class DAM extends mysqli{
	public function __construct(){
		//parent::__construct("mysql16.000webhost.com", "a6873773_c4", "cemp4t", "a6873773_c4");
		parent::__construct("localhost", "bukuk426_c4", "cemp4t", "bukuk426_bukukuliah");
		//parent::__construct("localhost", "root", "bima1992", "buku-kuliah");
	}
	
	public function create($tableName, $data){
		if($tableName == "pengguna"){
			$Username = $data->username;
			$Password = $data->password;
			$Lokasi = $data->lokasi;
			$URLFoto = $data->imageURL;
			$Email = $data->email;
			$Nama = $data->nama;
			$Deskripsi = $data->deskripsi;
			$query = "INSERT INTO `aktor_sistem` (`Username`, `Password`, `Lokasi`, `URLFoto`, `Email`, `Nama`, `Deskripsi`) 
								  VALUES('$Username', '$Password', '$Lokasi', '$URLFoto', '$Email', '$Nama', '$Deskripsi')";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "pesan"){
			$senderId = $data->senderId;
			$receiverId = $data->receiverId;
			$msgTime = $data->msgTime;
			$status = $data->status;
			$message = $data->message;
			$query = "INSERT INTO `pesan` (`ID`, `Isi_Pesan`, `Status_Pesan`, `Waktu_Pesan`, `IDPenerima`) 
								  VALUES('$senderId', '$message', '$status', '$msgTime', '$receiverId')";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "buku"){
			$query = "INSERT INTO `buku` (`ID`, `Judul`, `Penerbit`, `URLFoto`, `Edisi`, `Pengarang`,`Th_Terbit`) 
								  VALUES ('".$data->ID."','".$data->Judul."','".$data->Penerbit."','".$data->URLFoto."','".$data->Edisi."','".$data->Pengarang."','".$data->Th_Terbit."');";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "tag"){
			$query = "INSERT INTO `tag` (`Tag`, `IDBuku`) 
								  VALUES ('".$data['Tag']."','".$data['IDBuku']."');";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "resensi"){
			$query = "INSERT INTO `resensi` (`IDBuku`,  `IDPemberi`, `Isi_Resensi`, `Waktu_Resensi`) 
								  VALUES ('".$data['IDBuku']."','".$data['IDPemberi']."','".$data['Isi_Resensi']."','".$data['Waktu_Resensi']."');";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "reset_data"){
			$query = "INSERT INTO `reset_data` (`ID`,  `Hash`, `Expire_Time`) 
								  VALUES ('".$data['ID']."','".$data['Hash']."','".$data['Expire_Time']."');";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "memiliki_kategori"){
			$query = "INSERT INTO `memiliki_kategori` (`IDBuku`,  `IDKategori`) 
								  VALUES ('".$data['IDBuku']."','".$data['IDKategori']."');";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "rater_buku"){
			$query = "INSERT INTO `rater_buku` (`IDBuku`,  `ID`) 
								  VALUES ('".$data['IDBuku']."','".$data['ID']."');";
			//var_dump($query);exit(0);
			return parent::query($query);
		}
	}
	
	public function delete($tableName, $data){	
		if($tableName == "buku"){
			$whereStatement = "";
				foreach($data as $key=>$value){
					$whereStatement .= "$key='$value' AND ";
				}
			$whereStatement = substr($whereStatement, 0, strlen($whereStatement)-4);
			$query = "DELETE FROM `buku` WHERE $whereStatement";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "reset_data"){
			$whereStatement = "";
				foreach($data as $key=>$value){
					$whereStatement .= "$key='$value' AND ";
				}
			$whereStatement = substr($whereStatement, 0, strlen($whereStatement)-4);
			$query = "DELETE FROM `reset_data` WHERE $whereStatement";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "tag"){
			$whereStatement = "";
				foreach($data as $key=>$value){
					$whereStatement .= "$key='$value' AND ";
				}
			$whereStatement = substr($whereStatement, 0, strlen($whereStatement)-4);
			$query = "DELETE FROM `tag` WHERE $whereStatement";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "resensi"){
			$whereStatement = "";
				foreach($data as $key=>$value){
					$whereStatement .= "$key='$value' AND ";
				}
			$whereStatement = substr($whereStatement, 0, strlen($whereStatement)-4);
			$query = "DELETE FROM `resensi` WHERE $whereStatement";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "memiliki_kategori"){
			$whereStatement = "";
				foreach($data as $key=>$value){
					$whereStatement .= "$key='$value' AND ";
				}
			$whereStatement = substr($whereStatement, 0, strlen($whereStatement)-4);
			$query = "DELETE FROM `memiliki_kategori` WHERE $whereStatement";
			//var_dump($query);exit(0);
			return parent::query($query);
		}
	}
	
	
	public function retrieve($tableName, $data){
		if($tableName == "pengguna"){
			if($data === true){
				$query = "SELECT * FROM `aktor_sistem` WHERE 1";
			}else{	
				$whereStatement = "";
				foreach($data as $key=>$value){
					$whereStatement .= "$key='$value' AND ";
				}
				$whereStatement = substr($whereStatement, 0, strlen($whereStatement)-4);
				$query = "SELECT * FROM `aktor_sistem` WHERE $whereStatement";
			}
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "pesan"){
			$whereStatement = "";
			foreach($data as $key=>$value){
				$whereStatement .= "$key='$value' AND ";
			}
			$whereStatement = substr($whereStatement, 0, strlen($whereStatement)-4);
			$query = "SELECT * FROM `pesan` WHERE $whereStatement";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "pesan-pengguna"){
			$whereStatement = "";
			foreach($data as $key=>$value){
				$whereStatement .= "$key='$value' AND ";
			}
			$whereStatement = substr($whereStatement, 0, strlen($whereStatement)-4);
			$query = "SELECT aktor_sistem.ID as IDPengirim, pesan.IDPenerima as IDPenerima, aktor_sistem.Nama as Nama, Username,  IDPesan, Isi_Pesan, Status_Pesan, Waktu_Pesan  FROM `pesan` LEFT JOIN aktor_sistem ON pesan.ID = aktor_sistem.ID WHERE $whereStatement ORDER BY Waktu_Pesan DESC";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "buku"){		
			$bookData = array();
			if($data === true){		
				$counter = 0;
				$temp = $this->retrieve("buku-pengguna", $data);
				while($line = $temp->fetch_assoc()){
					$bookData[$counter]['info'] = $line;
					$data = array("buku.IDBuku"=>$line['IDBuku']);
					$data = $this->retrieve("kategori", $data);
					$cats = $this->toArray($data);
					$bookData[$counter++]['categories'] = $cats;
				}
			}else{
				$temp = $this->retrieve("buku-pengguna", $data);
				$bookData['info'] = $temp->fetch_assoc();
				
				$temp = $this->retrieve("resensi", $data);			
				$reviews = $this->toArray($temp);		
				$bookData['reviews'] = $reviews;
						
				$temp = $this->retrieve("kategori", $data);
				$cats = $this->toArray($temp);
				$bookData['categories'] = $cats;
				
				$temp = $this->retrieve("tag", array("IDBuku"=>$data['buku.IDBuku']));
				$cats = $this->toArray($temp);
				$bookData['tags'] = $cats;
			}
			//var_dump($bookData);echo "<br>"; exit(0);
			return $bookData;
		}else if($tableName == "reset_data"){
			$whereStatement = "";
			foreach($data as $key=>$value){
				$whereStatement .= "$key='$value' AND ";
			}
			$whereStatement = substr($whereStatement, 0, strlen($whereStatement)-4);
			$query = "SELECT * FROM `reset_data` WHERE $whereStatement";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "buku-pengguna"){
			if($data === true){
				$query = "SELECT aktor_sistem.ID as ID, Nama, IDBuku, Judul, Penerbit, buku.URLFoto, Edisi, Pengarang, Th_Terbit, Rating, Jumlah_Rater, Status, Username  FROM `buku` LEFT JOIN `aktor_sistem` ON `buku`.`ID` = `aktor_sistem`.`ID` WHERE 1";
			}else{
				$whereStatement = "";
				foreach($data as $key=>$value){
					$whereStatement .= "$key='$value' AND ";
				}
				$whereStatement = substr($whereStatement, 0, strlen($whereStatement)-4);
				$query = "SELECT aktor_sistem.ID as ID, Nama, IDBuku, Judul, Penerbit, buku.URLFoto, Edisi, Pengarang, Th_Terbit, Rating, Jumlah_Rater, Status, Username  FROM `buku` LEFT JOIN `aktor_sistem` ON `buku`.`ID` = `aktor_sistem`.`ID` WHERE $whereStatement";
			}
			//var_dump($query);echo "<br>"; var_dump(parent::query($query)); exit(0);
			return parent::query($query);
		}else if($tableName == "tag"){
			$whereStatement = "";
			foreach($data as $key=>$value){
				$whereStatement .= "$key='$value' AND ";
			}
			$whereStatement = substr($whereStatement, 0, strlen($whereStatement)-4);
			$query = "SELECT Tag  FROM `tag` WHERE $whereStatement";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "resensi"){
			$whereStatement = "";
			foreach($data as $key=>$value){
				$whereStatement .= "$key='$value' AND ";
			}
			$whereStatement = substr($whereStatement, 0, strlen($whereStatement)-4);
			//$query = "SELECT IDBuku, Nama, Isi_Resensi, Waktu_Resensi, Username  FROM `buku` LEFT JOIN `resensi` ON `buku`.`ID` = `resensi`.`IDPemberi` WHERE $whereStatement";
			$query = "SELECT Username, Nama,  IDBuku, Isi_Resensi, Waktu_Resensi, IDResensi FROM 
							(SELECT resensi.IDBuku as IDBuku, IDPemberi as ID, Isi_Resensi, IDResensi, Waktu_Resensi FROM `buku` LEFT JOIN `resensi` ON `buku`.`IDBuku` = `resensi`.`IDBuku` WHERE $whereStatement) sb LEFT JOIN aktor_sistem
								ON sb.ID = aktor_sistem.id WHERE 1 ORDER BY Waktu_Resensi DESC";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "kategori"){
			if($data === true){
				$query = "SELECT IDKategori , Nama_Kategori FROM kategori WHERE 1";
			}else{
				$whereStatement = "";
				foreach($data as $key=>$value){
					$whereStatement .= "$key='$value' AND ";
				}
				$whereStatement = substr($whereStatement, 0, strlen($whereStatement)-4);
				$query = "SELECT sb.IDKategori as IDKategori, Nama_Kategori FROM
							(SELECT memiliki_kategori.IDKategori as IDKategori FROM memiliki_kategori LEFT JOIN buku ON  memiliki_kategori.IDBuku = buku.IDBuku WHERE $whereStatement) sb LEFT JOIN kategori
							ON sb.IDKategori = kategori.IDKategori";
			}
			//var_dump($query);echo "<br>"; var_dump(parent::query($query)); exit(0);
			return parent::query($query);
		}else if($tableName == "rater_buku"){
			$whereStatement = "";
			foreach($data as $key=>$value){
				$whereStatement .= "$key='$value' AND ";
			}
			$whereStatement = substr($whereStatement, 0, strlen($whereStatement)-4);
			$query = "SELECT * FROM `rater_buku` WHERE $whereStatement";
			//var_dump($query);exit(0);
			return parent::query($query);
		}
	}
	
	public function update($tableName, $data, $cond){
		if($tableName == "pengguna"){
			$tableUpdate = "";
				foreach($data as $key=>$value){
					$tableUpdate .= "$key='$value', ";
				}
			$tableUpdate = substr($tableUpdate, 0, strlen($tableUpdate)-2);
			
			$whereStatement = "";
			foreach($cond as $key=>$value){
				$whereStatement .= "$key='$value' AND ";
			}
			$whereStatement = substr($whereStatement, 0, strlen($whereStatement)-4);
			
			
			$query = "UPDATE `aktor_sistem` SET $tableUpdate WHERE $whereStatement";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "buku"){
			$tableUpdate = "";
				foreach($data as $key=>$value){
					$tableUpdate .= "$key='$value', ";
				}
			$tableUpdate = substr($tableUpdate, 0, strlen($tableUpdate)-2);
			
			$whereStatement = "";
			foreach($cond as $key=>$value){
				$whereStatement .= "$key='$value' AND ";
			}
			$whereStatement = substr($whereStatement, 0, strlen($whereStatement)-4);
			
			
			$query = "UPDATE `buku` SET $tableUpdate WHERE $whereStatement";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "tag"){
			if($this->delete("tag", $cond)){
				foreach($data as $tag){
					if(!$this->create("tag", array("Tag"=>$tag, "IDBuku"=>$cond["IDBuku"])))
						return false;
				}
			}
			return true;
		}else if($tableName == "memiliki_kategori"){
			if($this->delete("memiliki_kategori", $cond)){
				foreach($data as $tag){
					if(!$this->create("memiliki_kategori", array("IDKategori"=>$tag, "IDBuku"=>$cond["IDBuku"])))
						return false;
				}
			}
			return true;
		}else if($tableName == "resensi"){
			$tableUpdate = "";
				foreach($data as $key=>$value){
					$tableUpdate .= "$key='$value', ";
				}
			$tableUpdate = substr($tableUpdate, 0, strlen($tableUpdate)-2);
			
			$whereStatement = "";
			foreach($cond as $key=>$value){
				$whereStatement .= "$key='$value' AND ";
			}
			$whereStatement = substr($whereStatement, 0, strlen($whereStatement)-4);
			
			
			$query = "UPDATE `resensi` SET $tableUpdate WHERE $whereStatement";
			//var_dump($query);exit(0);
			return parent::query($query);
		}else if($tableName == "pesan"){
			$tableUpdate = "";
				foreach($data as $key=>$value){
					$tableUpdate .= "$key='$value', ";
				}
			$tableUpdate = substr($tableUpdate, 0, strlen($tableUpdate)-2);
			
			$whereStatement = "";
			foreach($cond as $key=>$value){
				$whereStatement .= "$key='$value' AND ";
			}
			$whereStatement = substr($whereStatement, 0, strlen($whereStatement)-4);
			
			
			$query = "UPDATE `pesan` SET $tableUpdate WHERE $whereStatement";
			//var_dump(parent::query($query));exit(0);
			return parent::query($query);
		}
	}
	
	public function toArray($data){
		$result = array();
		if($data->num_rows > 0)
			while($line = $data->fetch_assoc()){
				$result[] = $line;
			}
		return $result;
	}
	
	public function search($data){
		$query = "SELECT aktor_sistem.ID as ID, Nama, IDBuku, Judul, Penerbit, buku.URLFoto, Edisi, Pengarang, Th_Terbit, Rating, Status, Username  FROM `buku` LEFT JOIN `aktor_sistem` ON `buku`.`ID` = `aktor_sistem`.`ID`
				  WHERE IDBuku in (SELECT IDBuku FROM `tag` WHERE `Tag` LIKE '%${data['terms']}%')
				  UNION
				  SELECT aktor_sistem.ID as ID, Nama, IDBuku, Judul, Penerbit, buku.URLFoto, Edisi, Pengarang, Th_Terbit, Rating, Status, Username  FROM `buku` LEFT JOIN `aktor_sistem` ON `buku`.`ID` = `aktor_sistem`.`ID` 
			  	  WHERE (match(Judul) against('${data['terms']}' IN BOOLEAN MODE)) OR  (match(Pengarang) against('${data['terms']}' IN BOOLEAN MODE)) OR  (match(Penerbit) against('${data['terms']}' IN BOOLEAN MODE))
				 ";
		
		//var_dump($query);exit(0);
		return parent::query($query);
	}
}
?>