<?php
/**
 * http://localhost/db_nextdoor/api/getFriendInfo?uid=2
 */
require_once 'api.php';
class getFriendInfo extends api {

	protected $bolCheckLogin = false;

	public function doExecute() {
		// in case of sql injection
		$uid = intval($_COOKIE['uid']);
		
		$friends = 'SELECT f.uid, f.friend_uid as friend_id, u.firstname, u.lastname, u.photo FROM friend f, user u WHERE u.uid=f.friend_uid AND f.uid=' . $uid . ';';

		$query = mysqli_query($this->conn, $friends);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if ($data) {
			return $data;
		} else {
			throw new Exception("No friends.");
		}
	}
}

try {
	$thisClass = new getFriendInfo;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>