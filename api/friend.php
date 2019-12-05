<?php
/**
 * http://localhost/db_nextdoor/api/friend?uid=2
 */
require_once 'api.php';
class friend extends api {
	public function doExecute() {
		// in case of sql injection
		pass



		if ($data) {
			return $data;
		} else {
			throw new Exception("No reply message.");
		}

	}
		public function getJson() {
		try {
			$this->res['data'] = $this->doExecute();
		} catch (Exception $e) {
			$this->res['status'] = -1;
			$this->res['message'] = $e->getMessage();
		} finally {
			echo json_encode($this->res);
		}
	}
}
$friends = new friend;
$data = $friends->getJson();
?>