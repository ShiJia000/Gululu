<?php
/**
 * http://localhost/db_nextdoor/api/getFriendInfo?uid=2
 */
require_once 'api.php';
class getFriendInfo extends api {
	public function doExecute() {
		// in case of sql injection
		$uid=intval($_GET['uid']);

		$friends='SELECT f.uid, f.friend_uid as friend_id, u.firstname, u.lastname, u.photo 
		FROM friend f, user u
		WHERE u.uid=f.friend_uid
		AND f.uid=' . $uid . ';';

		$query = mysqli_query($this->conn, $friends);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if ($data) {
			return $data;
		} else {
			throw new Exception("No friends.");
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
$friends = new getFriendInfo;
$data = $friends->getJson();
?>