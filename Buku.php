<?php
	class Buku{
		public $ID, $IDBuku, $Judul, $Penerbit, $URLFoto, $Edisi, $Pengarang, $Th_Terbit, $Rating;
		public function __construct($data){
			$this->ID = $data['ID'];
			$this->IDBuku = @$data['IDBuku'];
			$this->Judul = $data['Judul'];
			$this->Penerbit = $data['Penerbit'];
			$this->URLFoto = $data['URLFoto'];
			$this->Edisi = @$data['Edisi'];
			$this->Pengarang = $data['Pengarang'];
			$this->Th_Terbit = $data['Th_Terbit'];
			$this->Rating = isset($data['Rating'])?$data['Rating']:0;
		}
	}
?>