<?php
/**
 * @author     (Jia Shi <js11182@nyu.edu>)
 * send message to one person (one friend or neighbor)
 */

require_once'api.php';

class sendMsgToOnePerson extends api{
	
	public function doExecute() {
		$conn = $this->conn;
		$conn->autocommit(false);

		$this->title = $this->check($_POST['title']);
		$this->subject = $this->check($_POST['subject']);
		$this->uid = intval($_COOKIE['uid']);

		$this->textBody = $this->check($_POST['textBody']);
		$this->latitude = floatval($_POST['lat']);
		$this->longitude = floatval($_POST['lng']);
		$this->addr = $this->check($_POST['addr']);
		$this->tid = intval($_POST['tid']);

		// check not null
		$this->checkNotNull();

		$uidArr = array();
		// check has friends or block
		if ($this->tid === 3) {
			// check if this person has friends
			$uidArr = $this->getAllFriends();

		} else if ($this->tid === 4 || $this->tid === 5) {
			$bid = $this->checkHasBlock();

			if ($this->tid === 4) {
				// get all people in this block
				$uidArr = $this->getAllInBlock($bid);
			} else {
				// get all people in this hood 
				$uidArr = $this->getAllInHood($bid);
			}
			
		} else {
			throw new Exception("Wrong message type, you can only send message to friends, neighbor or hood", -1);
		}


		// insert data into message
		$msgSql = "INSERT INTO message (`title`, `subject`, `uid`, `text_body`, `lantitude`, `longitude` , `tid`, `address`) VALUES ('"
			. $this->title . "', '"
			. $this->subject . "', "
			. $this->uid . ", '"
			. $this->textBody . "', "
			. $this->latitude . ", "
			. $this->longitude . ", "
			. $this->tid . ", '"
			. $this->addr . "');";

		$conn->query($msgSql);
		$mid = $conn->insert_id;

		// insert data into receive message
		$receiveSql = "INSERT INTO receive_msg (`mid`, `uid`, `is_read`) VALUES (" . $mid . ", " . $this->uid . ", 1)";

		foreach ($uidArr as $uid) {
			$receiveSql .= ", (" . $mid . ", " . $uid . ", 0)";
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
	 * [checkHasFriends if the user has friends]
	 * @return [array]
	 */
	public function getAllFriends() {
		$res = array();
		$conn = $this->conn;
		// check if this person has friends
		$checkSql = "SELECT friend_uid FROM friend WHERE uid = " . $this->uid . " AND is_valid = 1;";
		$query = $conn->query($checkSql);
		$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($checkData) == 0) {
			throw new Exception("You don't have any friends, please add someone as your friend or wait for your friend accept your request.", -1);
		}

		// get all friends of this person
		foreach ($checkData as $k => $v) {
			array_push($res, intval($v['friend_uid']));
		}
		return $res;
	}

	/**
	 * [checkHasBlock check if this user joined a block or a hood]
	 * @return [type] []
	 */
	public function checkHasBlock() {
		$conn = $this->conn;

		$checkSql = "SELECT * FROM join_block WHERE uid = " . $this->uid . " AND is_approved = 1;";
		$query = $conn->query($checkSql);
		$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($checkData) == 0) {
			throw new Exception("You didn't join a block, please join a block or wait for your neighbor to approve your join request.", -1);
		}

		$bid = $checkData[0]['bid'];
		return $bid;
	}

	/**
	 * [getAllInBlock get all uids in this block]
	 * @param  [type] $bid [bid]
	 * @return [type]      [description]
	 */
	public function getAllInBlock($bid) {
		$conn = $this->conn;
		$res = array();

		$sql = "SELECT uid FROM join_block WHERE is_approved = 1 AND uid <> " . $this->uid . " AND bid = " . $bid . ";";
		$query = $conn->query($sql);
		$uidData = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($uidData) == 0) {
			throw new Exception("There is no other people in this block except yourself.", -1);
		}

		foreach ($uidData as $k => $v) {
			array_push($res, intval($v['uid']));
		}

		return $res;
	}

	/**
	 * [getAllInHood get all uids in this hood]
	 * @param  [type] $bid [description]
	 * @return [type]      [description]
	 */
	public function getAllInHood($bid) {
		$conn = $this->conn;
		$res = array();

		$sql = "SELECT uid FROM join_block jb, block b WHERE jb.is_approved=1 AND jb.bid = b.bid AND uid <> " . $this->uid . " AND hid = (SELECT hid FROM block WHERE bid = " . $bid . " LIMIT 1);";
		$query = $conn->query($sql);
		$uidData = mysqli_fetch_all($query, MYSQLI_ASSOC);

		foreach ($uidData as $k => $v) {
			array_push($res, intval($v['uid']));
		}

		return $res;
	}

	/**
	 * [checkNotNull description]
	 * @return [type] [void]
	 */
	public function checkNotNull() {

		if (!$this->title) {
			throw new Exception("Message title cannot be null!");
		}
		if (!$this->subject) {
			throw new Exception("Message subject cannot be null!");
		}
		if (!$this->uid) {
			throw new Exception("Message sender cannot be null!");
		}
		if (!$this->textBody) {
			throw new Exception("Message content cannot be null!");
		}
		if (!$this->tid) {
			throw new Exception("Message type cannot be null!");
		}
	}
}

try {
	$thisClass = new sendMsgToOnePerson;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>