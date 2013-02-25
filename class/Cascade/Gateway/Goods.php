 <?php
	class Cascade_Gateway_Goods extends Cascade_Proxy_CustomGateway {
		public function getAllItems() {
			return $this->session->find ( 'find_all' );
		}
	}
?>