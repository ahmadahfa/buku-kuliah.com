<?php
	class Pesan{
		public $senderId, $receiverId, $msgTime, $status, $message;
		public function __construct($data){
			$this->senderId = $data['senderId'];
			$this->receiverId = $data['receiverId'];
			$this->msgTime = isset($data['msgTime'])?$data['msgTime']:date("Y-m-d H:i:s");
			$this->status = isset($data['status'])?$data['status']:0;
			$this->message = $data['message'];
		}
	}
?>