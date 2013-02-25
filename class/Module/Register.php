<?php
final class Module_Register {
	private $user_ac;
	public function __construct() {
		$this->user_ac = Cascade::getAccessor('Register');
	}

	public function getItem($id) {
		return $this->user_ac->get($id);
	}

	public function getAll() {
		return $this->user_ac->getAllItems();
	}
	
	public function getloginInfo($name,$password){
		$params = array('name'=>$name,'password'=>$password);
		return $this->user_ac->userloginInfo($params);
	}
	
	public function checkExistUser($name){
		return $this->user_ac->checkExistUser($name);
	}

	public function addNewUser($name, $pwd, $phone, $email){
		$params = array('name' => $name, 'password' => $pwd, 'phone' => $phone, 'email' => $email);
		return $this->user_ac->addNewUser($params);
	}
	
	public function checkLogin($name,$password){
		$params = array('name' => $name, 'password' => $password);
		return $this->user_ac->userloginInfo($params);
	}
	
	public function getLastInsertId(){
		return $this->user_ac->lastInsertId();
	}
	
	public function minusCoin($id,$price){
		$params = array('id'=>$id, 'price'=>$price);
		return $this->user_ac->minusCoin($params);
	}
	
	public function getAllById($id){
		$params = array('id'=>$id);
		return $this->user_ac->get($id);
	}
		
}

