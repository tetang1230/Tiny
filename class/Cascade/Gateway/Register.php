 <?php
	class Cascade_Gateway_Register extends Cascade_Proxy_CustomGateway {
		/*
		 *
		 */
		
		public function userloginInfo($params) {
			return $this->session->find ( 'login', $params );
		}

	    public function getAllItems() {
			return $this->session->find ( 'find_all' );
		}
		
		public function checkExistUser($param){
			return $this->session->find( 'checkExistUser',$param );
		}
		
		public function addNewUser($params){
			return $this->session->execute('addNewUser', $params);
		}
		
		public function minusCoin($params){
			return $this->session->execute('minusCoin', $params);
		}
	}
?>