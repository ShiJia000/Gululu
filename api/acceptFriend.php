<?php
/**
 * http://localhost/db_nextdoor/api/acceptFriend
 */

require_once 'api.php';
class acceptFriend extends api {

	protected $bolCheckLogin = false;

	public function doExecute() {
		// in case of sql injection
		$conn = $this->conn;
		$this->uid = intval($_POST['uid']);
		$this->friend_uid = intval($_POST['friend_uid']);


		//check duplicate relationship

		 
		$check_friend = 'SELECT * FROM friend WHERE uid = ' . $this->uid . ' AND friend_uid = ' . $this->friend_uid. ' AND (is_valid = 1 or is_valid = -1);';
		$query = $conn->query($check_friend);
		$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($checkData) != 0){
			throw new Exception("Error, Already sent a invitation!");
		}

		//check uid & friend not in the same hood
		$check_hood = 'SELECT * FROM (SELECT hid FROM join_block j, block b WHERE j.uid=' . $this->uid . ' AND j.bid=b.bid AND is_approved = 1) as a, join_block j, block b WHERE j.uid=' . $this->friend_uid . ' AND j.bid=b.bid AND is_approved = 1 AND a.hid=b.hid';

		$query = $conn->query($check_hood);
		$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($checkData) == 0){
			throw new Exception("Error, Cannot add because not in the same hood!");
		}

		//add friends
		$friends = 'INSERT INTO friend (`uid`, `friend_uid`, `is_valid`) VALUES (' . $this->uid . ', ' . $this->friend_uid . ', 0);';

		$data = $conn->query($friends);

		if ($data) {
			return $data;
		} else {
			throw new Exception("No friends.");
		}
	}
}

try {
	$thisClass = new acceptFriend;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}
?>