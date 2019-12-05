<?php
/**
 * http://localhost/db_nextdoor/api/getReplyMessage?uid=2
 */
require_once 'api.php';
class getReplyMessage extends api {
	public function doExecute() {
		// in case of sql injection
		$mid=intval($_GET['mid']);
		$replyMessage='';


		$replyMessage='SELECT u.firstname, u.lastname, u.photo, r.*
						FROM reply r, user u
						WHERE u.uid=r.uid
						AND r.mid= ' .$mid . ';';

		
		$query = mysqli_query($this->conn, $replyMessage);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

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
$Replys = new getReplyMessage;
$data = $Replys->getJson();
?>