<?php
/**
 * http://localhost/db_nextdoor/api/getBlockFeed?uid=1
 */
require_once 'api.php';
class getBlockFeed extends api {
	public function doExecute() {
		// in case of sql injection
		$uid = intval($_GET['uid']);
		$isUnRead = intval($_GET['unread']);
		$getBlockMsg = '';
		if ($isUnRead === 1) {
			// unread messages
			$getBlockMsg = 'SELECT u.firstname, u.lastname,u.photo, m.*
			FROM receive_msg rm, message m, user u
			WHERE rm.mid = m.mid
			AND u.uid = m.uid 
			AND is_read = 0 
			AND m.tid = 4
			AND rm.uid = ' . $uid . ';'; 
		} else {
			// all messages from friends
			$getBlockMsg = 'SELECT u.firstname, u.lastname,u.photo, m.*
			FROM receive_msg rm, message m, user u
			WHERE rm.mid = m.mid 
			AND u.uid = m.uid 
			AND m.tid = 4 
			AND rm.uid = ' . $uid . ';'; 
		}
		$query = mysqli_query($this->conn, $getBlockMsg);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);
		if ($data) {
			return $data;
		} else {
			throw new Exception("No message about block.");
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
$blockFeed = new getBlockFeed;
$data = $blockFeed->getJson();
?>