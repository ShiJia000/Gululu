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
		$conn->autocommit(false);

		$this->uid = intval($_COOKIE['uid']);
		$this->friend_uid = intval($_POST['friend_uid']);
		$this->is_valid = intval($_POST['is_valid']);

		$this->checkNotNull();

		//check is_valid value
		if ($this->is_valid == 0){
			throw new Exception("Error Processing Request", 1);
		}
		 
		//check
		$check_friend = 'SELECT * FROM friend WHERE uid = ' . $this->uid . ' AND friend_uid = ' . $this->friend_uid. ' AND is_valid <> ' . $this->is_valid . ';';
		$query = $conn->query($check_friend);
		$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($checkData) == 0){
			throw new Exception("Error");
		}

		//add/cancel friends
		$friends1 = 'UPDATE friend SET is_valid = ' . $this->is_valid . ' WHERE uid = ' . $this->uid . ' AND friend_uid = ' . $this->friend_uid . ';';
		$friends2 = 'UPDATE friend SET is_valid = ' . $this->is_valid . ' WHERE friend_uid = ' . $this->uid . ' AND uid = ' . $this->friend_uid . ';';

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
		if (!$this->is_valid){
			throw new Exception("is_valid cannot be NULL!");
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