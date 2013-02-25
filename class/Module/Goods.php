<?php
final class Module_Goods {
	private $buyStaff_ac;
	
	public function __construct() {
		$this->buyStaff_ac = Cascade::getAccessor('Goods');
	}
	
	public function getAll() {
		return $this->buyStaff_ac->getAllItems();
	}

}