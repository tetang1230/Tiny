<?php
class IndexController extends Controller {

	public function testAction(){
		echo 'this is test action';
	}
	
	public function indexAction(){
		
		$m_g = new Module_Goods();
		
		$all_data = $m_g->getAll();
		print_r($all_data);
		
		echo 'this is index action';
	}
}