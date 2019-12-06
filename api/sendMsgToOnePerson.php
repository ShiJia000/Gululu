<?php
/**
 * @author     (Jia Shi <js11182@nyu.edu>)
 * send message to one person (one friend or neighbor)
 */

require_once'api.php';

class sendMsgToOnePerson extends api
{
	
	public function doExecute() {
		$conn = $this->conn;
		$conn->autocommit(false);

		$this->title = $this->check($_POST['title']);
		$this->subject = $this->check($_POST['subject']);
		$this->uid = intval($_POST['postUid']);

		$this->textBody = $this->check($_POST['textBody']);
		$this->latitude = floatval($_POST['latitude']);
		$this->longitude = floatval($_POST['longitude']);
		$this->tid = intval($_POST['tid']);
		$this->receiveUid = intval($_POST['receiveUid']);

		// check not null
		$this->checkNotNull();

		// check is valid neighbor or friend
		if ($this->tid === 1) {
			$checkSql = "SELECT * FROM friend WHERE uid = " . $this->uid . " AND friend_uid = " . $this->receiveUid . " AND is_valid = 1;";

			$query = $conn->query($checkSql);
			$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

			if (count($checkData) == 0) {
				throw new Exception("The user you sent message to is not your friend.", -1);
			}

		} else if ($this->tid === 2) {
			$checkSql = "SELECT * FROM friend WHERE uid = " . $this->uid . " AND neighbor_uid = " . $this->receiveUid . " AND is_valid = 1;";

			$query = $conn->query($checkSql);
			$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

			if (count($checkData) == 0) {
				throw new Exception("The user you sent message to is not your neighbor.", -1);
			}

		} else {
			throw new Exception("Wrong message type, you can only send message to a friend or a neighbor!", -1);		
		}
		
		// insert data into message
		$msgSql = "INSERT INTO message (`title`, `subject`, `uid`, `text_body`, `lantitude`, `longitude` , `tid`) VALUES ('"
			. $this->title . "', '"
			. $this->subject . "', "
			. $this->uid . ", '"
			. $this->textBody . "', "
			. $this->latitude . ", "
			. $this->longitude . ", "
			. $this->tid . ");";

		$conn->query($msgSql);
		$mid = $conn->insert_id;

		// insert data into receive message
		$receiveSql = "INSERT INTO receive_msg (`mid`, `uid`, `is_read`) VALUES (" . $mid . ", " . $this->uid . ", 1), (" . $mid . ", " . $this->receiveUid . ", 0)";
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
	 * [getJson to json format]
	 * @return [type] [description]
	 */
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
		if (!$this->receiveUid) {
			throw new Exception("Message receiver cannot be null!");
		}
		if (!$this->tid) {
			throw new Exception("Message type cannot be null!");
		}
	}
}

$sendMsgToOnePerson = new sendMsgToOnePerson;
$data = $sendMsgToOnePerson->getJson();

?>