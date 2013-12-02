<?php
	class PageController{
		public static function dispatch($action, $data = NULL){
			
			if($data == NULL){
				//var_dump($data);exit(0);
				PageController::load($action.".tpt");
				
			}else{
				if($action == "daftar-pengguna"){		
					require("PenggunaManager.php");
					try{
						$pm = new PenggunaManager();
						$success = $pm->createPengguna($data);
						if($success){
							unset($_SESSION['daftar-pengguna.tpt']);
							PageController::load("daftar-pengguna.tpt", array("message"=>"Registration success. Detail of your new account has been sent to your email address"));
						}else{
							$data["message"] = "Registration Failed. An error happens when saving to database.";
							PageController::load("daftar-pengguna.tpt", $data);
						}
					}catch(Exception $e){
						$data["message"] = $e->getMessage();
						PageController::load("daftar-pengguna.tpt", $data);
					}
				}else if($action == "masuk"){	
					require("PenggunaManager.php");
					$pm = new PenggunaManager();
					define("SUCCESS", 0);
					define("INVALID_USERNAME", 1);
					define("INVALID_PASSWORD", 2);
					$authData = $pm->authenticate($data);
				
					//var_dump($authData);exit(0);					
					if($authData['status'] == SUCCESS){
						$p = $authData["data"]->fetch_assoc();
						PageController::setSessionData($p);
						if($p['isAdmin'] == 1)
							PageController::load("halaman-admin.tpt", array("message"=>"Login Success", "data"=>$p));
						else
							PageController::load("halaman-utama.tpt", array("message"=>"Login Success", "data"=>$p));						
					}else if($authData['status'] == INVALID_USERNAME){
						PageController::load("halaman-utama.tpt", array("message"=>"Username is not found."));
					}else if($authData['status'] == INVALID_PASSWORD){
						PageController::load("halaman-utama.tpt", array("message"=>"Wrong password"));
					}
					
				}else if($action == "keluar"){
					PageController::setSessionData();
					PageController::load("halaman-utama.tpt", array("message"=>"Logout success"));
				}else if($action == "lihat-profil"){
					
					if(isset($_SESSION['lihat-profil.tpt']))
						unset($_SESSION['lihat-profil.tpt']);
					if(isset($_SESSION['lihat-profil-pengguna-lain.tpt']))
						unset($_SESSION['lihat-profil-pengguna-lain.tpt']);
						
						
					$p = PageController::getSessionData();
					if($data === true){ //looking his own profile
						if($p == NULL){
							PageController::load("halaman-utama.tpt", array("message"=>"You are not logged in."));
						}else{							
							include_once("BukuManager.php");
							$pm = new BukuManager();
							$books = $pm->getAllBukuByPengguna(array("aktor_sistem.ID"=>$p['ID']));
							$data = array();
							while($line = $books->fetch_assoc()){
								$data['books'][] = $line;
							}
							PageController::load("lihat-profil.tpt", $data);
						}
					}else if(is_array($data)){
						require("PenggunaManager.php");
						$pm = new PenggunaManager();
						$temp = $pm->getPengguna($data);
						
						//var_dump($userInfo->fetch_assoc());exit(0);
						if($temp === false || $temp->num_rows == 0){
							PageController::load("halaman-utama.tpt", array("message"=>"Username you specified is not exists."));
						}
						
						$userInfo = $temp->fetch_assoc();
						
						require("BukuManager.php");
						$bm = new BukuManager();
						$books = $bm->getAllBukuByPengguna(array("aktor_sistem.ID"=>$userInfo['ID']));
						$data = array();
						while($line = $books->fetch_assoc()){
							$data['books'][] = $line;
						}
						
						//var_dump(array_merge($data, $userInfo)); exit(0);
						
						PageController::load("lihat-profil-pengguna-lain.tpt", array_merge($data, $userInfo));
					}
				}else if($action == "pesan-masuk"){	
					if(isset($_SESSION['pesan-masuk.tpt']))
						unset($_SESSION['pesan-masuk.tpt']);
					require("PesanManager.php");
					
					$p = PageController::getSessionData();					
					$pm = new PesanManager();
					
					if($data === true){
						$userId = $p['ID'];
						$p = $pm->getAllPesanByPengguna($userId);
						$data = array();
						while($line = $p->fetch_assoc()){
							$data[] = $line;
						}
						//var_dump($p); exit(1);	
						PageController::load("pesan-masuk.tpt", $data);
					}else{
						$pesanId = $data;
						$p = $pm->getPesan($pesanId);
						$p = $p->fetch_assoc();
						//var_dump($p); exit(1);	
						PageController::load("info-pesan.tpt", $p);
					}
				}else if($action == "kirim-pesan"){
					if(isset($_SESSION['kirim-pesan.tpt']))
						unset($_SESSION['kirim-pesan.tpt']);
						
					require("PesanManager.php");
					$pm = new PesanManager();
					$p = PageController::getSessionData();
					if(isset($data["receiverName"])){
						$data = array("to"=>$data["receiverName"]);
						PageController::load("kirim-pesan.tpt", $data);
					}
					
					
					try{
						$userId = $p['ID'];
						$data['senderId'] = $userId;
						$pm->kirimPesan($data);
						$p = $pm->getAllPesanByPengguna($userId);
						$data = array();
						while($line = $p->fetch_assoc()){
							$data[] = $line;
						}
						$data["message"] = "Message has been successfully sent.";
						//var_dump($p); exit(1);	
						unset($_SESSION['kirim_pesan']);
						PageController::load("pesan-masuk.tpt", $data);
					}catch(Exception $e){
						$data["message"] = $e->getMessage();
						PageController::load("kirim-pesan.tpt", $data);
					}
				}else if($action == "tambah-buku"){		
					require("BukuManager.php");
					$pm = new BukuManager();
					try{
						$pm->addBuku($data);
						unset($_SESSION[$action.'.tpt']);
						PageController::load($action.'.tpt', array("message"=>"Congratulation, a new book has been pushed to your repo."));
					}catch(Exception $e){
						$data["message"] = $e->getMessage();
						PageController::load($action.'.tpt', $data);
					}
				}else if($action == "hapus-buku"){
					include_once("BukuManager.php");
					$bm = new BukuManager();
					$n = count($data);
					if($_SESSION['SessionData']['isAdmin'] == 0){
						foreach($data as $id){					
							//var_dump($id);exit(0);
							if(!$bm->deleteBuku(array("IDBuku"=>$id))){
								$_SESSION['PageController']['message'] = "Unable to delete book with id ".$id;
								PageController::dispatch("lihat-profil", true);
							}
						}
						$_SESSION['PageController']['message'] = "$n book(s) have been trashed.";
						PageController::dispatch("lihat-profil", true);
					}
				}else if($action == "prareset-password"){
					require("PenggunaManager.php");
					$pm = new PenggunaManager();	
					
					$temp = $pm->getPengguna($data);
					$email = $data["Email"];
					if($temp === false || $temp->num_rows == 0){
						$data['message'] = "Your email is not associated to any account.";
						PageController::load("prareset-password.tpt", $data);
					}else if($temp->num_rows == 1){ // Got you
						$userInfo = $temp->fetch_assoc();
						$hash = PageController::getRandomChars(20);
						$data = array("ID"=>$userInfo['ID'], "Hash"=>$hash, "Expire_Time"=>date("Y-m-d H:i:s", strtotime("+10 minutes")));
						$recoveryLink = "http://buku-kuliah.com/controller.php?dispatch=validasi-email&hash=$hash";
						//var_dump($data);exit(0);
						$pm->savePasswordRecoveryData($data);
						$to     = $email;
						$nama 	= $userInfo['Nama'];
						$subject = 'Password recovery';
						$message = "Hello $nama, please visit $recoveryLink to recover your password. ". "\r\n". "\r\n". "\r\n". "\r\n". "Do not reply this message";
						$headers = 'From: noreply@buku-kuliah.com' . "\r\n" .
							'X-Mailer: PHP/' . phpversion();

						if(mail($to, $subject, $message, $headers)){
							$data = array("message"=>"Recovery link has been sent to your email. The link will be expired in 10 minutes. Hurry Up! ");
							PageController::load("prareset-password.tpt", $data);
						}
						
					}
				}else if($action == "validasi-email"){
					require("PenggunaManager.php");
					$pm = new PenggunaManager();
					
					$temp = $pm->getPasswordRecoveryData($data);
					if($temp->num_rows == 0){
						PageController::load("prareset-password.tpt", array("message"=>"Hash is not valid. Are you trying to break in our system?"));	
					}
					$data = $temp->fetch_assoc();
					
					$expireTime = new DateTime($data['Expire_Time']);
					$now = new DateTime();
					$interval =  date_diff($now, $expireTime); 
					$isValid = ($interval->format('%R')) == '+';
					
					if($isValid){					
						$_SESSION['userId'] = $data['ID'];
						PageController::load("reset-password.tpt", null);
					}else{
						$data = array("message"=>"Recovery link has been expired. Duuh!! ");
						PageController::load("prareset-password.tpt", $data);
					}
					
					//var_dump($data);exit(0);
				}else if($action == "reset-password"){
					//var_dump($data);exit(0);
					unset($_SESSION['userId']);
					require("PenggunaManager.php");
					$pm = new PenggunaManager();
					if($pm->reset($data)){
						$data = array("message"=>"Your password has been reset.  ");
						PageController::load("halaman-utama.tpt", $data);
					}else{
						$data = array("message"=>"Your password cannot be reset. Contact admin!");
						PageController::load("prareset-password.tpt", $data);
					}
				}else if($action == "ubah-profil"){
					require("PenggunaManager.php");
					$pm = new PenggunaManager();
					if($pm->editPengguna($data)){
						$temp = $pm->getPengguna(array("ID"=>$data['ID']));
						$p = $temp->fetch_assoc();
						PageController::setSessionData($p);
						
						require("BukuManager.php");
						$bm = new BukuManager();
						$books = $bm->getAllBukuByPengguna(array("aktor_sistem.ID"=>$p['ID']));
						$data = array();
						while($line = $books->fetch_assoc()){
							$data['books'][] = $line;
						}
						$data['message'] = "Your profile is now update.";
						PageController::load("lihat-profil.tpt", $data);
					}
					$data['message'] = "Updating failed. Please try again later.";
					PageController::load("ubah-profil.tpt", null);
				}else if($action == "info-buku"){
					require("BukuManager.php");
					$bm = new BukuManager();
					$data = $bm->getBuku($data);
					$p = PageController::getSessionData();
					if($bm->hasRated(array("IDBuku"=>$data['info']['IDBuku'], "ID"=>$p['ID'])))
						$data['canRate'] = false;
					//var_dump($data);exit(0);
					PageController::load("info-buku.tpt", $data);
				}else if($action == "ubah-info-buku"){
					require("BukuManager.php");
					$bm = new BukuManager();
					
					$session = PageController::getSessionData();
					$bookId = $data["buku.IDBuku"];
					$temp = $bm->getAllBukuByPengguna(array("buku.ID"=>$session['ID'], "buku.IDBuku"=>$bookId));
					if($temp->num_rows == 0)
						PageController::load("halaman-utama.tpt", array("message"=>"You're not authorized to edit the book."));
					
					if(count($data) == 1){					
						$bookInfo = $bm->getBuku($data);
						//var_dump($bookInfo['reviews'][count($bookInfo['reviews'])-1]);exit(0);
						$tags = $bookInfo['tags'];					
						$temp = array();
						foreach($tags as $tag){
							$temp[] = $tag['Tag'];
						}
						$tags = implode(",", $temp);					
						$reviews = $bookInfo['reviews'][count($bookInfo['reviews'])-1]['Isi_Resensi'];
						
						$temp = $bookInfo['categories'];
						$cats = array();
						foreach($temp as $cat){
							$cats[] = $cat['IDKategori'];
						}
						
						$data = $bookInfo['info'];
						$data['Tags'] = $tags;
						$data['Kategori']  = $cats;
						$data['Resensi'] = $reviews;
						$data["buku.IDBuku"] = $bookId;
						$data["IDResensi"] = $bookInfo['reviews'][count($bookInfo['reviews'])-1]["IDResensi"];
						//var_dump($data);exit(0);
						PageController::load("ubah-info-buku.tpt", $data);
					}else{
						//var_dump($data);exit(0);
						$cond = array("buku.ID"=>$session['ID'], "buku.IDBuku"=>$bookId);
						unset($data["buku.IDBuku"]);
						try{
							if($bm->editBuku($data, $cond)){
								$data = $bm->getBuku(array("buku.IDBuku"=>$bookId));
								$data['message'] = "Book has been successfully updated";
								PageController::load("info-buku.tpt", $data);
							}else{
								$data = $bm->getBuku(array("buku.IDBuku"=>$bookId));
								$data['message'] = "Book data cannot be updated";
								PageController::load("info-buku.tpt", $data);
							}
						}catch(Exception $e){
							$data = $bm->getBuku(array("buku.IDBuku"=>$bookId));
							$data['message'] = $e->getMessage();
							PageController::load("info-buku.tpt", $data);
						}
					}
				}else if($action == "lihat-pesan"){
					require("PesanManager.php");					
					$p = PageController::getSessionData();					
					$pm = new PesanManager();
					$temp = $pm->getPesan($data);
					$msg = $temp->fetch_assoc();
					//var_dump($msg);exit(0);
					if($msg && $msg['IDPenerima']==$p['ID']){
						$data = $msg;
						$prevId = $nextId = null;
						$temp = $pm->getAllPesanByPengguna($p['ID']);						
						//var_dump($msg);exit(0);
						$found = false;
						while(($line = $temp->fetch_assoc()) && !$found){
							//var_dump($line);exit(0);
							if($line['IDPesan'] == $msg['IDPesan']){
								$line = $temp->fetch_assoc();
								$nextId = isset($line['IDPesan'])?$line['IDPesan']:null;
								$found = true;
							}else
								$prevId = $line['IDPesan'];
						}
						$data['next'] = $nextId;
						$data['previous'] = $prevId;
						//var_dump($data);exit(0);
						PageController::load("lihat-pesan.tpt", $data);
					}else{
						$data = array("message"=>"There's possibility the message has been deleted.");
						PageController::load("halaman-utama.tpt", $data);
					}
				}else if($action == "resensi"){
					$now = date("Y-m-d H:i:s");
					$p = PageController::getSessionData();	
					$id = $p['ID'];
					$data['Waktu_Resensi'] = $now;
					$data['IDPemberi'] = $id;
					
					require("BukuManager.php");
					$bm = new BukuManager();
					if($bm->addResensi($data)){
						$data = $bm->getBuku(array("buku.IDBuku"=>$data['IDBuku']));
						$data['message'] = "Your review has been added.";
						PageController::load("info-buku.tpt", $data);
					}
					$data['message'] = "Your review can not be added.";
					PageController::load("info-buku.tpt", $data);
				}else if($action == "pencarian"){
					require("BukuManager.php");								
					$pm = new BukuManager();
					$keyword = implode(" ",preg_split("/\s+/",trim($data['keywords'])));
					$offset = $data['page']*10;
					$temp = $pm->search(array("terms"=>$keyword, "offset"=>$offset));
					$n = $temp->num_rows;
					$data = array();
					$data['keyword'] = $keyword;
					while($line = $temp->fetch_assoc()){
						$temp2 = $pm->getKategori(array("buku.IDBuku"=>$line['IDBuku']));
						$cats = array();
						while($cat = $temp2->fetch_assoc()){
							$cats[] = $cat["Nama_Kategori"];
						}
						$line['Kategori'] = implode(", ", $cats);
						$data['books'][] = $line;
					}
					if($offset+10 < $n)
						$data['next'] = true;
					if($offset > 10)
						$data['previous'] = true;
					//var_dump($data);exit(0);
					PageController::load("hasil-pencarian.tpt", $data);
				}else if($action == "rate"){
					require("BukuManager.php");								
					$pm = new BukuManager();
					$success = $pm->rate($data);
					var_dump($success);exit(0);
				}else if($action == "lihat-daftar-pengguna"){
					require("PenggunaManager.php");								
					$pm = new PenggunaManager();
					$temp = $pm->getAllPengguna();
					$data = array();
					while($line = $temp->fetch_assoc()){
						if($line['isAdmin'] == 0)
							$data['users'][] = $line;
					}
					//var_dump($data);exit(0);
					PageController::load("lihat-daftar-pengguna.tpt", $data);
				}else if($action == "lihat-daftar-buku"){
					require("BukuManager.php");								
					$pm = new BukuManager();
					$all = $pm->getAllBuku();
					$cats = array();
					foreach($all as $line){
						foreach($line['categories'] as $cat){
							$cats[$cat["Nama_Kategori"]][] = $line['info'];
						}
					}
					//var_dump($cats);exit(0);
					PageController::load("lihat-daftar-buku.tpt", array('data'=>$cats));
				}
			}
		}
		
		public static function load($template, $data = NULL){
			if(isset($_SESSION['PageController']['message'])){
				$data['message'] = $_SESSION['PageController']['message'];
				unset($_SESSION['PageController']['message']);
			}
			if($data != NULL)	
				$_SESSION[$template] = $data;	
			header("Location: view.php?p=".$template);
			exit(0);
		}
		
		public static function validate($data){
			$retVal = array();
			foreach($data as $key=>$value){
				$retVal[$key] = $this->db->real_escape_string($value);
			}
			return $retVal;
		}
		
		public static function setSessionData($p = null){
			if($p == null){
				session_destroy();
			}else
				$_SESSION['SessionData'] = $p;
		}
		public static function getSessionData(){
			return (isset($_SESSION['SessionData']))?$_SESSION['SessionData']:null;
		}
		
		public static function getRandomChars($n){
			$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
			$str = "";
			for($i=0; $i<20; $i++)
				$str .= substr($chars, rand()%62, 1);
			return $str;
		}
	}
?>