<?php
/**
 * http://localhost/db_nextdoor/api/getBlockFeed?uid=1
 */
require_once 'api.php';
class getBlockFeed extends api {

	protected $bolCheckLogin = false;

	public function doExecute() {
		// in case of sql injection
		$uid = intval($_COOKIE['uid']);
		$isUnRead = intval($_GET['unread']);
		$getBlockMsg = '';
		if ($isUnRead === 1) {
			// unread messages
			$getBlockMsg = 'SELECT u.firstname, u.lastname, u.photo, m.*
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
		
		foreach ($data as $key => $value) {
			$mid = $value['mid'];

			$replyMessage = 'SELECT u.firstname, u.lastname, u.photo, r.* FROM receive_reply rr, reply r, user u, message m WHERE u.uid = r.uid AND m.mid = r.mid AND m.uid = rr.uid AND rr.rid = r.rid AND is_read = 0 AND r.mid = ' .$mid . ';';

			$query = mysqli_query($this->conn, $replyMessage);
			$replyData = mysqli_fetch_all($query, MYSQLI_ASSOC);
			$data[$key]['reply'] = $replyData;
		}
		return $data;
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

try {
	$blockFeed = new getBlockFeed;
	$blockFeed->res['data'] = $blockFeed->doExecute();

} catch (Exception $e) {
	$blockFeed->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$blockFeed->res['message'] = $e->getMessage();

} finally {
	echo json_encode($blockFeed->res);
}

?>