 <?php
	class Cascade_Gateway_BuyStaff extends Cascade_Proxy_CustomGateway {
		/*
		 *
		 */
		/*public function userloginInfo($params) {
			return $this->session->find ( 'login', $params );
		}
		
		public function checkExistUser($param){
			return $this->session->find( 'checkExistUser',$param );
		}*/
		
		public function getAllItems() {
			return $this->session->find ( 'find_all' );
		}
		
		public function addNewItem($params){
			return $this->session->execute('addNewItem', $params);
		}
		
		public function updateItem($params){
			return $this->session->execute('updateItem', $params);
		}
		
		public function retrieveBag($params){
			return $this->session->find('retrieveBag', $params);
		}
	}
?>