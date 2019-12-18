<?php
/**
 * http://localhost/db_nextdoor/api/getNeighborFeed?uid=2
 */
require_once 'api.php';
class getNeighborFeed extends api {

	public function doExecute() {
		// in case of sql injection
		$uid = intval($_COOKIE['uid']);
		$isUnRead = intval($_GET['unread']);
		$getNeighborMsg = '';
		if ($isUnRead === 1) {
			// unread messages
			$getNeighborMsg = 'SELECT u.firstname, u.lastname,u.photo, m.*
			FROM receive_msg rm, message m, user u
			WHERE rm.mid = m.mid
			AND u.uid = m.uid 
			AND is_read = 0 
			AND m.tid = 2 
			AND rm.uid = ' . $uid . ';'; 
		} else {
			// all messages from friends
			$getNeighborMsg = 'SELECT u.firstname, u.lastname,u.photo, m.*
			FROM receive_msg rm, message m, user u
			WHERE rm.mid = m.mid 
			AND u.uid = m.uid 
			AND m.tid = 2 
			AND rm.uid = ' . $uid . ';'; 
		}
		$query = mysqli_query($this->conn, $getNeighborMsg);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);
		
		foreach ($data as $key => $value) {
			$mid = $value['mid'];

			$replyMessage = 'SELECT u.firstname, u.lastname, u.photo, r.* FROM receive_reply rr, reply r, user u WHERE rr.rid = r.rid AND r.uid = u.uid AND rr.uid= ' . $uid . ' AND r.mid = ' .$mid . ';';

			$query = mysqli_query($this->conn, $replyMessage);
			$replyData = mysqli_fetch_all($query, MYSQLI_ASSOC);
			$data[$key]['reply'] = $replyData;
		}
		return $data;
	}

}

try {
	$neighborFeed = new getNeighborFeed;
	$neighborFeed->res['data'] = $neighborFeed->doExecute();

} catch (Exception $e) {
	$neighborFeed->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$neighborFeed->res['message'] = $e->getMessage();

} finally {
	echo json_encode($neighborFeed->res);
}

?>
