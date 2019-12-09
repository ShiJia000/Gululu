<?php
/**
 * @author     (Jia Shi <js11182@nyu.edu>)
 * send reply to
 */

require_once'api.php';

class sendReply extends api{
	
	public function doExecute() {
		$conn = $this->conn;
		$conn->autocommit(false);

		$this->uid = intval($_COOKIE['uid']);
		$this->mid = intval($_POST['mid']);
		$this->content = $this->check($_POST['content']);

		// update all the msg to read
		$updateMsg = "UPDATE receive_msg SET is_read = 1 WHERE uid = " . $this->uid . " AND mid = " . $this->mid . ";";
		$conn->query($updateMsg);

		// update all the reply to read
		$updateReply = "UPDATE receive_reply rr, reply r SET rr.is_read = 1 WHERE rr.rid = r.rid AND r.mid = " . $this->mid . " AND rr.uid = " . $this->uid . ";";
		$conn->query($updateReply);

		// insert into reply
		$insertReply = "INSERT INTO reply (`mid`, `uid`, `content`) VALUES (" . $this->mid . ", " . $this->uid . ", '" . $this->content . "');";
		$conn->query($insertReply);
		$rid = $conn->insert_id;

		
		// get users who can receive this msg
		$getUsers = "select uid from receive_msg where mid = " . $this->mid . " AND uid <> " . $this->uid . ";";
		$query = $conn->query($getUsers);
		$uidArr = mysqli_fetch_all($query, MYSQLI_ASSOC);

		// insert into receive_reply
		$receiveSql = "INSERT INTO receive_reply (`rid`, `uid`, `is_read`) VALUES (" . $rid . ", " . $this->uid . ", 1)";
		foreach ($uidArr as $uid) {
			$receiveSql .= ", (" . $rid . ", " . $uid['uid'] . ", 0)";
		}
		$receiveSql .= ";";
		$conn->query($receiveSql);

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
	public function checkNotNull() {

		if (!$this->uid) {
			throw new Exception("uid cannot be null!");
		}

		if (!$this->mid) {
			throw new Exception("mid cannot be null!");
		}

		if (!$this->content) {
			throw new Exception("content cannot be null!");
		}

	}
}

try {
	$thisClass = new sendReply;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>