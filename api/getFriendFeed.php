<?php
/**
 * http://localhost/db_nextdoor/api/getFriendFeed?uid=1
 */
require_once 'api.php';

class getFriendFeed extends api {
	public function friendFeed() {
		// in case of sql injection
		$uid = intval($_GET['uid']);
		$isUnRead = intval($_GET['unread']);
		$getFriendMsg = '';

		if ($isUnRead === 1) {
			// unread messages
			$getFriendMsg = 'SELECT m.* FROM receive_msg rm, message m, user u WHERE rm.mid = m.mid AND u.uid = m.uid AND is_read = 0 AND (m.tid = 1 or m.tid = 3) AND rm.uid = ' . $uid . ';'; 
		} else {
			// all messages from friends
			$getFriendMsg = 'SELECT m.* FROM receive_msg rm, message m, user u WHERE rm.mid = m.mid AND u.uid = m.uid AND (m.tid = 1 or m.tid = 3) AND rm.uid = ' . $uid . ';'; 
		}

		$query = mysqli_query($this->conn, $getFriendMsg);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if ($data) {
			return $data;
		} else {
			throw new Exception("No message about friends.");
		}
	}
}

$friendFeed = new getFriendFeed;
$data = $friendFeed->friendFeed();
$friendFeed->getJsonRes($data);
?>