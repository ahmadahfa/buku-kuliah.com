<?php
	session_start();
	define("TEMPLATE_PATH", "templates/");
	
	class Viewer{
		public static function display($template){
			if(!file_exists($template)){
				$_SESSION['halaman-utama.tpt']['message'] = "Page not found.";
				require("./".TEMPLATE_PATH.'halaman-utama.tpt');
				return;
			}
			include($template);
		}
	}	
	
	$page = $_GET['p'];
	Viewer::display("./".TEMPLATE_PATH.$page);
?>