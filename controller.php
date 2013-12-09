<?php
	session_start();
	require("PageController.php");	
	
	//$uploadDir = "C:\\Program Files\\Apache Software Foundation\\Apache2.2\\htdocs\\buku-kuliah\\uploads\\";
	$uploadDir = "/home/bukuk426/public_html/uploads/";
	
	$action = $_REQUEST['dispatch'];
	if($action == "daftar-pengguna"){
		if(isset($_POST['username'])){				
			if(preg_match('/^[A-Za-z0-9\-_\.]{6,40}/', $_POST['username']) == 0){
				$_SESSION[$action.".tpt"] = $_POST;
				$_SESSION[$action.".tpt"]["message"] = "username must contain alphanumeric, -, _, dot, and having 6 - 15 characters of length";
				//var_dump($_SESSION[$action.".tpt"]);exit(0);
				$_SESSION[$action.".tpt"]["username"] = "";
				PageController::load("daftar-pengguna.tpt", $_SESSION[$action.".tpt"]);	
			}
			
			if(preg_match('/^[A-Za-z \.]{6,40}/', $_POST['nama']) == 0){
				$_SESSION[$action.".tpt"] = $_POST;
				$_SESSION[$action.".tpt"]["message"] = "Name must contain only alphabets, whitespaces and having 6 - 40 characters of length";
				$_SESSION[$action.".tpt"]["nama"] = "";
				PageController::load("daftar-pengguna.tpt", $_SESSION[$action.".tpt"]);
			} 
				
			if(preg_match('/^[A-Za-z \._-]{3,40}@[A-Za-z \._-]{3,20}/', $_POST['email']) == 0){
				$_SESSION[$action.".tpt"] = $_POST;
				$_SESSION[$action.".tpt"]["message"] = "Email format is unacceptable.";
				$_SESSION[$action.".tpt"]["email"] = "";
				PageController::load("daftar-pengguna.tpt", $_SESSION[$action.".tpt"]);
			}
			
			if(preg_match('/^[A-Za-z0-9\-_\.]{6,40}/', $_POST['password']) == 0){
				$_SESSION[$action.".tpt"] = $_POST;
				$_SESSION[$action.".tpt"]["password"] = "";
				$_SESSION[$action.".tpt"]["password2"] = "";
				$_SESSION[$action.".tpt"]["message"] = "Password must contain alphanumeric, -, _, dot, and having 6 - 15 characters of length";
				PageController::load("daftar-pengguna.tpt", $_SESSION[$action.".tpt"]);	
			}
			
			if($_POST['password'] !== $_POST['password2']){
				$_SESSION[$action.".tpt"] = $_POST;
				$_SESSION[$action.".tpt"]["password"] = "";
				$_SESSION[$action.".tpt"]["password2"] = "";
				$_SESSION[$action.".tpt"]["message"] = "Passwords you typed are mismatch.";
				PageController::load("daftar-pengguna.tpt", $_SESSION[$action.".tpt"]);	
			}
			
			if($_FILES['picture']['error']){
				$_SESSION[$action.".tpt"] = $_POST;
				$_SESSION[$action.".tpt"]["message"] = "Choose a photo to upload.";
				PageController::load("daftar-pengguna.tpt", $_SESSION[$action.".tpt"]);
			}
			
			$uploadFile = $uploadDir .$_POST['username']."-". basename($_FILES['picture']['name']);
			
			$data = array();
			$data['username'] = $_POST['username'];
			$data['password'] = md5($_POST['password']);
			$data['email'] = $_POST['email'];
			$data['lokasi'] = $_POST['lokasi'];
			$data['nama'] = $_POST['nama'];
			$data['deskripsi'] = $_POST['deskripsi'];
			
			/*if (!move_uploaded_file($_FILES['picture']['tmp_name'], $uploadFile)){
				var_dump($_FILES['picture']);exit(0);
				$_SESSION[$action.".tpt"] = $_POST;
				$_SESSION[$action.".tpt"]["message"] = "Pho";
				PageController::load("daftar-pengguna.tpt", $_SESSION[$action.".tpt"]);
			}*/
						
			$data['imageURL'] = "uploads/".$_POST['username']."-". basename($_FILES['picture']['name']);		
			//PageController::dispatch($action, $data);
			PageController::load($action.".tpt", array("message"=>"We are currently in alpha testing. No registration is allowed. Thanks."));
			
		}else{
			PageController::dispatch($action);
		}
	}else if($action == "masuk"){
		$data = array();
		$data["username"] = $_POST["username"];
		$data["password"] = md5($_POST["password"]);
		PageController::dispatch($action, $data);
	}else if($action == "keluar"){
		if(PageController::getSessionData()){
			PageController::setSessionData();
			PageController::load("halaman-utama.tpt", array("message"=>"Logout success"));
		}else{
			PageController::load("halaman-utama.tpt", array("message"=>"You haven't logged in yet."));
		}
	}else if($action == "lihat-profil"){
		if(!isset($_GET['user'])){
			if(PageController::getSessionData()){		
				PageController::dispatch($action, true);
			}else{
				PageController::load("halaman-utama.tpt", array("message"=>"You haven't logged in yet."));
			}
		}else{
			PageController::dispatch($action, array("username"=>$_GET['user']));
		}
	}else if($action == "pesan-masuk"){
		PageController::dispatch($action, isset($_GET['id'])?$_GET['id']:true);
	}else if($action == "kirim-pesan"){
		if(PageController::getSessionData()){
			if(isset($_POST['to']) && $_POST['to'] != ''){
				if(isset($_POST['subject']) && $_POST['subject'] != ''){
					if(isset($_POST['message']) && $_POST['message'] != ''){
						$data = array("to"=>$_POST['to'], "subject"=>$_POST['subject'], "message"=>"[".$_POST['subject']."] ".$_POST['message']);
						//var_dump($data);exit(0);
						PageController::dispatch($action, $data);
					}else{
						$data = array("to"=>$_POST['to'], "subject"=>$_POST['subject'], "message"=>"Content of your message can not be empty.");
						PageController::load("kirim-pesan.tpt", $data);
					}
				}else{
					$data = array("to"=>$_POST['to'], "message"=>"Put a simple subject to the form.");
					PageController::load("kirim-pesan.tpt", $data);
				}
			}else{
				if(isset($_GET['user'])){
					PageController::dispatch($action, array("receiverName"=>$_GET['user']));
				}else{
					PageController::dispatch($action);
				}
			}
		}else{
			PageController::load("halaman-utama.tpt", array("message"=>"You haven't logged in yet."));
		}
	}else if($action == "tambah-buku"){
		if(PageController::getSessionData()){		
			if(isset($_POST['Judul'])){	
				if(preg_match('/.{3,}/', $_POST['Judul']) == 0){
					$_SESSION[$action.".tpt"] = $_POST;
					$_SESSION[$action.".tpt"]["message"] = "Book title is too short.";
					$_SESSION[$action.".tpt"]["Judul"] = "";
					PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);
				} 
				if(preg_match('/^[0-9]{1,4}/', $_POST['Edisi']) == 0){
					$_SESSION[$action.".tpt"] = $_POST;
					$_SESSION[$action.".tpt"]["message"] = "Book edition is an integer, right?";
					$_SESSION[$action.".tpt"]["Edisi"] = "";
					PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);
				} 
				
				
				if(preg_match('/.{2,}/', $_POST['Pengarang']) == 0){
					$_SESSION[$action.".tpt"] = $_POST;
					$_SESSION[$action.".tpt"]["Pengarang"] = "";
					$_SESSION[$action.".tpt"]["message"] = "Specify the book author";
					PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);	
				}
				
				if(preg_match('/.{2,}/', $_POST['Penerbit']) == 0){
						$_SESSION[$action.".tpt"] = $_POST;
						$_SESSION[$action.".tpt"]["Penerbit"] = "";
						$_SESSION[$action.".tpt"]["message"] = "Specify the book publisher";
						PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);	
				}
				
				
				if(preg_match('/^[0-9]{4}/', $_POST['Th_Terbit']) == 0){
					$_SESSION[$action.".tpt"] = $_POST;
					$_SESSION[$action.".tpt"]["message"] = "Can't you write the right year?";
					$_SESSION[$action.".tpt"]["Th_Terbit"] = "";
					PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);
				} 
				
				if(!isset($_POST['Kategori'])){
						$_SESSION[$action.".tpt"]["message"] = "Specify the category of the book";
						PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);	
				}
					
				if(preg_match('/.{2,}/', $_POST['Tags']) == 0){
						$_SESSION[$action.".tpt"] = $_POST;
						$_SESSION[$action.".tpt"]["Tags"] = "";
						$_SESSION[$action.".tpt"]["message"] = "Specify at least a tag of the book";
						PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);	
				}
			
								
				if(preg_match('/^.{10,}/', $_POST['Resensi']) == 0){
					$_SESSION[$action.".tpt"] = $_POST;
					$_SESSION[$action.".tpt"]["Resensi"] = "";
					$_SESSION[$action.".tpt"]["message"] = "Put at least 10 characters to describe the book.";
					PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);	
				}
				
				if($_FILES['picture']['error']){
					$_SESSION[$action.".tpt"] = $_POST;
					$_SESSION[$action.".tpt"]["message"] = "Choose a photo to upload.";
					PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);
				}
				
				
				$temp;
				preg_match_all("/^.*\.([a-zA-Z0-9]{1,})/i", $_FILES['picture']['name'], $temp);
				$extension = $temp[1][0];
				$randomChars = PageController::getRandomChars(20);			
				$uploadFile = $uploadDir.$randomChars.".".$extension;
				//var_dump($uploadFile);exit(0);
				
				$data = array();
				$data['Judul'] = $_POST['Judul'];
				$data['Edisi'] = @$_POST['Edisi'];
				$data['Pengarang'] = $_POST['Pengarang'];
				$data['Penerbit'] = $_POST['Penerbit'];
				$data['Kategori'] = $_POST['Kategori'];
				$data['Tags'] = explode(",",$_POST['Tags']);	
				$data['Resensi'] = $_POST['Resensi'];
				$data['Th_Terbit'] = $_POST['Th_Terbit'];
				
				$p = PageController::getSessionData();				
				$data['ID'] = $p['ID'];
				$data['URLFoto'] = "uploads/".$randomChars.".".$extension;	
				
				//var_dump($data);exit(0);
				
				if (!move_uploaded_file($_FILES['picture']['tmp_name'], $uploadFile)){
					//var_dump($_FILES['picture']);exit(0);
					$_SESSION[$action.".tpt"] = $_POST;
					$_SESSION[$action.".tpt"]["message"] = "Photo cannot be moved. Check your file.";
					PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);
				}
							
				$data['URLFoto'] = "uploads/".$randomChars.".".$extension;	
				//var_dump($data);exit(0);
				
				PageController::dispatch($action, $data);
				
			}else{
				PageController::dispatch($action);
			}
		}else{
			PageController::load("halaman-utama.tpt", array("message"=>"You aren't logged in yet."));
		}
	}else if($action == "hapus-buku"){
		if(PageController::getSessionData()){
			//var_dump($_POST); exit(0);
			$data = $_POST['id'];
			PageController::dispatch($action, $data);
		}else{
			PageController::load("halaman-utama.tpt", array("message"=>"You aren't logged in yet."));
		}
	}else if($action == "prareset-password"){
		$data = null;
		if(isset($_POST['Email'])){
			$data = array("Email"=>$_POST['Email']);
		}
		PageController::dispatch($action, $data);
	}else if($action == "validasi-email"){
		$data = null;
		if(isset($_GET['hash'])){
			$data = array("Hash"=>$_GET['hash']);
		}
		PageController::dispatch($action, $data);
	}else if($action == "reset-password"){
		if(isset($_POST['Password']) && isset($_SESSION['userId'])){
			if(preg_match('/^[A-Za-z0-9\-_\.]{6,40}/', $_POST['Password']) == 0){
				$_SESSION[$action.".tpt"]["message"] = "Password must contain alphanumeric, -, _, dot, and having 6 - 15 characters of length";
				PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);	
			}
			
			if($_POST['Password'] !== $_POST['Password2']){
				$_SESSION[$action.".tpt"]["message"] = "Passwords are mismatch.";
				PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);	
			}
						
			$data = array("Password"=>md5($_POST['Password']), "ID"=>$_SESSION['userId']);
		
			PageController::dispatch($action, $data);
		}else{
			PageController::load("halaman-utama.tpt", array("message"=>"You're not authorized"));
		}		
	}else if($action == "ubah-profil"){
		if(PageController::getSessionData()){	
			if(isset($_POST['Password']) || isset($_POST['Nama']) || isset($_POST['Email']) || isset($_POST['Lokasi']) || isset($_POST['Deskripsi'])){
				//var_dump($_FILES);exit(0);
				if($_POST['Password'] == "sd9fhsd82398sda19823asdfgf")
					unset($_POST['Password']);
				
				$data = array();	
					
				if(preg_match('/^[A-Za-z \.]{6,40}/', $_POST['Nama']) == 0){
					$_SESSION[$action.".tpt"] = $_POST;
					$_SESSION[$action.".tpt"]["message"] = "Name must contain only alphabets, whitespaces and having 6 - 40 characters of length";
					$_SESSION[$action.".tpt"]["Nama"] = "";
					PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);
				} 
				$data['Nama'] = $_POST['Nama'];
				
					
				if(preg_match('/^[A-Za-z \._-]{3,40}@[A-Za-z \._-]{3,20}/', $_POST['Email']) == 0){
					$_SESSION[$action.".tpt"] = $_POST;
					$_SESSION[$action.".tpt"]["message"] = "Email format is unacceptable.";
					$_SESSION[$action.".tpt"]["Email"] = "";
					PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);
				}
				$data['Email'] = $_POST['Email'];
				
				
				//password handling
				if(isset($_POST['Password'])){
					if(preg_match('/^[A-Za-z0-9\-_\.]{6,40}/', $_POST['Password']) == 0){
						$_SESSION[$action.".tpt"] = $_POST;
						$_SESSION[$action.".tpt"]["Password"] = "";
						$_SESSION[$action.".tpt"]["Password2"] = "";
						$_SESSION[$action.".tpt"]["message"] = "Password must contain alphanumeric, -, _, dot, and having 6 - 15 characters of length";
						PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);	
					}
					
					if($_POST['Password'] !== $_POST['Password2']){
						$_SESSION[$action.".tpt"] = $_POST;
						$_SESSION[$action.".tpt"]["Password"] = "";
						$_SESSION[$action.".tpt"]["Password2"] = "";
						$_SESSION[$action.".tpt"]["message"] = "Passwords you typed are mismatch.";
						PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);	
					}					
					$data['Password'] = md5($_POST['Password']);
				}
				
				
				if($_FILES['picture']["name"] != ""){					
					if($_FILES['picture']['error']){
						$_SESSION[$action.".tpt"] = $_POST;
						$_SESSION[$action.".tpt"]["message"] = "Upload error!!";
						PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);
					}
					
					$temp;
					preg_match_all("/^.*\.([a-zA-Z0-9]{1,})/i", $_FILES['picture']['name'], $temp);
					$extension = $temp[1][0];
					$randomChars = PageController::getRandomChars(20);			
					$uploadFile = $uploadDir.$randomChars.".".$extension;
					
					if (!move_uploaded_file($_FILES['picture']['tmp_name'], $uploadFile)){
						//var_dump($_FILES['picture']);exit(0);
						$_SESSION[$action.".tpt"] = $_POST;
						$_SESSION[$action.".tpt"]["message"] = "Pho";
						PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);
					}
								
					$data['URLFoto'] = "uploads/".$randomChars.".".$extension;	
				}
				
				$p = PageController::getSessionData();
				$data['ID'] = $p['ID'];				
				$data['Lokasi'] = $_POST['Lokasi'];
				$data['Deskripsi'] = $_POST['Deskripsi'];
				
				//var_dump($data);exit(0);
				PageController::dispatch($action, $data);
				
			}else{
				PageController::dispatch($action);
			}
		}else{
			PageController::load("halaman-utama.tpt", array("message"=>"You aren't logged in yet."));
		}
	}else if($action == "info-buku"){
		$bookId = isset($_GET['id'])?$_GET['id']:0;
		PageController::dispatch($action, array("buku.IDBuku"=>$bookId));
	}else if($action == "ubah-info-buku"){
		if(PageController::getSessionData()){		
			if(isset($_POST['Judul']) || isset($_POST['Edisi']) || isset($_POST['Pengarang']) || isset($_POST['Th_Terbit']) || isset($_POST['Kategori'])){	
				$idBuku = $_SESSION[$action.".tpt"]['buku.IDBuku'];
				$data = array();
				if(preg_match('/.{3,}/', $_POST['Judul']) == 0){
					$_SESSION[$action.".tpt"] = $_POST;
					$_SESSION[$action.".tpt"]['buku.IDBuku'] = $idBuku;
					$_SESSION[$action.".tpt"]["message"] = "Book title is too short.";
					$_SESSION[$action.".tpt"]["Judul"] = "";
					PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);
				} 			
				if(preg_match('/^[0-9]{1,4}/', $_POST['Edisi']) == 0){
					$_SESSION[$action.".tpt"] = $_POST;
					$_SESSION[$action.".tpt"]["message"] = "Book edition is an integer, right?";
					$_SESSION[$action.".tpt"]["Edisi"] = "";
					$_SESSION[$action.".tpt"]['buku.IDBuku'] = $idBuku;
					PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);
				} 
				
				
				if(preg_match('/.{2,}/', $_POST['Pengarang']) == 0){
					$_SESSION[$action.".tpt"] = $_POST;
					$_SESSION[$action.".tpt"]["Pengarang"] = "";
					$_SESSION[$action.".tpt"]["message"] = "Specify the book author";
					$_SESSION[$action.".tpt"]['buku.IDBuku'] = $idBuku;
					PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);	
				}
				
				if(preg_match('/.{2,}/', $_POST['Penerbit']) == 0){
						$_SESSION[$action.".tpt"] = $_POST;
						$_SESSION[$action.".tpt"]["Penerbit"] = "";
						$_SESSION[$action.".tpt"]["message"] = "Specify the book publisher";
					$_SESSION[$action.".tpt"]['buku.IDBuku'] = $idBuku;
						PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);	
				}
				
				
				if(preg_match('/^[0-9]{4}/', $_POST['Th_Terbit']) == 0){
					$_SESSION[$action.".tpt"] = $_POST;
					$_SESSION[$action.".tpt"]["message"] = "Can't you write the right year?";
					$_SESSION[$action.".tpt"]["Th_Terbit"] = "";
					$_SESSION[$action.".tpt"]['buku.IDBuku'] = $idBuku;
					PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);
				} 
				
				if(!isset($_POST['Kategori'])){
						$_SESSION[$action.".tpt"]["message"] = "Specify at least a  category of the book";
					$_SESSION[$action.".tpt"]['buku.IDBuku'] = $idBuku;
						PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);	
				}
					
				if(preg_match('/.{2,}/', $_POST['Tags']) == 0){
						$_SESSION[$action.".tpt"] = $_POST;
						$_SESSION[$action.".tpt"]["Tags"] = "";
						$_SESSION[$action.".tpt"]["message"] = "Specify at least a tag of the book";
					$_SESSION[$action.".tpt"]['buku.IDBuku'] = $idBuku;
						PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);	
				}
			
								
				if(preg_match('/^.{10,}/', $_POST['Resensi']) == 0){
					$_SESSION[$action.".tpt"] = $_POST;
					$_SESSION[$action.".tpt"]["Resensi"] = "";
					$_SESSION[$action.".tpt"]["message"] = "Put at least 10 characters to describe the book.";
					$_SESSION[$action.".tpt"]['buku.IDBuku'] = $idBuku;
					PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);	
				}
				
				if($_FILES['picture']["name"] != ""){	
					if($_FILES['picture']['error']){
						$_SESSION[$action.".tpt"] = $_POST;
						$_SESSION[$action.".tpt"]["message"] = "Choose a photo to upload.";
					$_SESSION[$action.".tpt"]['buku.IDBuku'] = $idBuku;
						PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);
					}
					
					$temp;
					preg_match_all("/^.*\.([a-zA-Z0-9]{1,})/i", $_FILES['picture']['name'], $temp);
					$extension = $temp[1][0];
					$randomChars = PageController::getRandomChars(20);			
					$uploadFile = $uploadDir.$randomChars.".".$extension;
					//var_dump($uploadFile);exit(0);					
									
					if (!move_uploaded_file($_FILES['picture']['tmp_name'], $uploadFile)){
						$_SESSION[$action.".tpt"] = $_POST;
						$_SESSION[$action.".tpt"]["message"] = "Photo cannot be moved. Check your file.";
					$_SESSION[$action.".tpt"]['buku.IDBuku'] = $idBuku;
						PageController::load($action.".tpt", $_SESSION[$action.".tpt"]);
					}
								
					$data['URLFoto'] = "uploads/".$randomChars.".".$extension;	
				}
				
				$data['Judul'] = $_POST['Judul'];
				$data['Edisi'] = @$_POST['Edisi'];
				$data['Pengarang'] = $_POST['Pengarang'];
				$data['Penerbit'] = $_POST['Penerbit'];
				$data['Kategori'] = $_POST['Kategori'];
				$data['Tags'] = explode(",",$_POST['Tags']);	
				$data['Resensi'] = $_POST['Resensi'];
				$data['Th_Terbit'] = $_POST['Th_Terbit'];
				$data['Status'] = isset($_POST['Status'])?$_POST['Status']:0;
				$data['buku.IDBuku'] = $_SESSION[$action.".tpt"]['buku.IDBuku'];
				$data['IDResensi'] = $_SESSION[$action.".tpt"]['IDResensi'];
				//var_dump($data);exit(0);
				
				PageController::dispatch($action, $data);
				
			}else{
				PageController::dispatch($action, array("buku.IDBuku"=>$_GET['id']));
			}
		}else{
			PageController::load("halaman-utama.tpt", array("message"=>"You aren't logged in yet."));
		}
	}else if($action == "lihat-pesan"){
		//var_dump($_GET['id']); exit(0);
		if(PageController::getSessionData()){
			PageController::dispatch($action, $_GET['id']);
		}else{
			PageController::load("halaman-utama.tpt", array("message"=>"You aren't authorized."));
		}
	}else if($action == "resensi"){
		if(PageController::getSessionData()){
			if(preg_match('/.{10,}/', $_POST['Isi_Resensi']) == 0){	
				$_SESSION["PageController"]["message"] = "Review is too short.";			
				PageController::dispatch("info-buku", array("buku.IDBuku"=>$_POST['IDBuku']));
			} 	
			PageController::dispatch($action, array("Isi_Resensi"=>$_POST['Isi_Resensi'], "IDBuku"=>$_POST['IDBuku']));
		}else{
			PageController::load("halaman-utama.tpt", array("message"=>"You aren't authorized."));
		}
	}else if($action == "pencarian"){
		$data = array("keywords"=>$_GET["keywords"], "page"=>isset($_GET["page"])?$_GET["page"]:0);
		PageController::dispatch($action, $data);
	}else if($action == "rate"){
		$data = array("Rating"=>$_GET["rating"], "IDBuku"=>$_GET["id"]);
		PageController::dispatch($action, $data);
	}else if($action == "lihat-daftar-pengguna"){
		PageController::dispatch($action, true);
	}else if($action == "lihat-daftar-buku"){
		PageController::dispatch($action, true);
	}else{
		PageController::load("halaman-utama.tpt", array("message"=>"Function has not been implemented yet."));
	}
?>