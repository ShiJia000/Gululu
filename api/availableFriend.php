<?php
/**
 * http://localhost/db_nextdoor/api/availableFriend?uid=2
 */
require_once 'api.php';
class availableFriend extends api {

	protected $bolCheckLogin = false;

	public function doExecute() {
		// in case of sql injection

		// $uid = intval($_GET['uid']);
		$uid = intval($_COOKIE['uid']);
		

		$sql = 'SELECT u.uid, u.firstname, u.lastname, b.bname FROM user u, join_block jb, block b,(SELECT jb.uid, hid FROM join_block jb, block b WHERE jb.uid=' . $uid . ' AND jb.bid=b.bid AND jb.is_approved=1) as t WHERE u.uid = jb.uid AND jb.bid=b.bid AND b.hid = t.hid AND is_approved=1 AND u.uid<>' . $uid . ' AND u.uid NOT IN (SELECT friend_uid FROM friend WHERE uid=' . $uid . ' AND is_valid=1);';

		// var_dump($sql);

		$query = mysqli_query($this->conn, $sql);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if ($data) {
			return $data;
		} else {
			throw new Exception("No friends.");
		}
	}
}

try {
	$thisClass = new availableFriend;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>