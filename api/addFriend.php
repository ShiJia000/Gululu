<?php
/**
 * http://localhost/db_nextdoor/api/addFriend
 */

require_once 'api.php';
class addFriends extends api {

	public function doExecute() {
		// in case of sql injection
		$conn = $this->conn;
		$conn->autocommit(false);
		$this->uid = intval($_COOKIE['uid']);
		$this->friend_uid = intval($_POST['friend_uid']);

		$this->checkNotNull();

		//check duplicate relationship
		$check_friend = 'SELECT * FROM friend WHERE uid = ' . $this->uid . ' AND friend_uid = ' . $this->friend_uid. ' AND is_valid = 1;';
		$query = $conn->query($check_friend);
		$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($checkData) != 0){
			throw new Exception("Error, exists duplicate relationship!");
		}

		//Duplicate entry '9-8' for key 'PRIMARY'
		$check = 'SELECT * FROM friend WHERE uid = ' . $this->uid . ' AND friend_uid = ' . $this->friend_uid. ';';
		$query = $conn->query($check);
		$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if(count($checkData)!=0){
			$friends1 = 'UPDATE friend SET is_valid = 0 WHERE friend_uid = ' . $this->uid . ' AND uid = ' . $this->friend_uid . ';';
			$friends2 = 'UPDATE friend SET is_valid = 0 WHERE uid = ' . $this->uid . ' AND friend_uid = ' . $this->friend_uid . ';';
		}else{
			//check uid & friend not in the same hood
			$check_hood = 'SELECT * FROM (SELECT hid FROM join_block j, block b WHERE j.uid=' . $this->uid . ' AND j.bid=b.bid AND is_approved = 1) as a, join_block j, block b WHERE j.uid=' . $this->friend_uid . ' AND j.bid=b.bid AND is_approved = 1 AND a.hid=b.hid';

			$query = $conn->query($check_hood);
			$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

			if (count($checkData) == 0){
				throw new Exception("Error, Cannot add because not in the same hood!");
			}

			//add friends
			$friends1 = 'INSERT INTO friend (`uid`, `friend_uid`, `is_valid`) VALUES (' . $this->uid . ', ' . $this->friend_uid . ', 0);';

			$friends2 = 'INSERT INTO friend (`uid`, `friend_uid`, `is_valid`) VALUES (' . $this->friend_uid . ' , ' . $this->uid . ' , 0);';
		}

		$conn->query($friends1);
		$conn->query($friends2);
	
		// transaction
		if (!$conn->errno) {
		    $conn->commit();
		    return 'Success';
		} else {
		    $conn->rollback();
		    throw new Exception("An error Occurred!");
		}
	}

	/**
	 * [checkNotNull description]
	 * @return [type] [void]
	 */
	public function checkNotNull(){
		if (!$this->uid) {
			throw new Exception("uid cannot be NULL!");
		}
		if (!$this->friend_uid){
			throw new Exception("friend_uid cannot be NULL!");
		}
	}
}

try {
	$thisClass = new addFriends;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}
?>