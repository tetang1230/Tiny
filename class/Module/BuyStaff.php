<?php
final class Module_BuyStaff {
	private $buyStaff_ac;
	public function __construct() {
		$this->buyStaff_ac = Cascade::getAccessor('BuyStaff');
		//$this->buyStaff_mem = Cascade::getAccessor('TestKvs');
	}

	public function getItem($id) {
		return $this->buyStaff_ac->get($id);
	}

	public function getAll() {
		return $this->buyStaff_ac->getAllItems();
	}
	
	public function addNewItem($user_name,$goods_id){
		$params = array('user_name' => $user_name, 'goods_id' => $goods_id);
		$this->buyStaff_mem->set('newItem',$params);	
		return $this->buyStaff_ac->addNewItem($params);
	}
	
	public function updateItem($item,$name){
		$params = array('item' => $item, 'user_name' => $name);
		return $this->buyStaff_ac->updateItem($params);
	}
	
	public function retrieveBag($name){
		$params = array('user_name' => $name);
		return $this->buyStaff_ac->retrieveBag($params);
	}
	
	public function testGetBagMem(){
		$testMem = $this->buyStaff_mem->get('newItem');
		if(!empty($testMem)){
			return $testMem;
		}
		return null;
	}
}