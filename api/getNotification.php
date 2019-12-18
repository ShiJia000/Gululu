<?php
/**
 * @author     (Jia Shi <js11182@nyu.edu>)
 * http://localhost/db_nextdoor/api/getNotification
 */

require_once 'api.php';

class getNotification extends api {

	public function doExecute() {

		$this->uid = intval($_COOKIE['uid']);
			
		// get all add friend notification
		$friendData = $this->getFriendNoti();
		$friendNotiNum = count($friendData);

		// get all add neighbor notification
		$neighborData = $this->getNeiNoti();
		$neighborNotiNum = count($neighborData);

		// get all join block notification
		$joinBlockData = $this->getJoinBlockNoti();
		$joinNotiNum = count($joinBlockData);

		// get all notification num
		
		$num = $friendNotiNum + $neighborNotiNum + $joinNotiNum;

		$data = array(
			'totalNum' => $num,
			'notifications' => array(
				'friendNoti' => $friendData,
				'neighborNoti' => $neighborData,
				'joinBlockNoti' => $joinBlockData
			)
		);

		return $data;
	}

	/**
	 * [getFriendNoti get friend notification]
	 * @return [type] [description]
	 */
	private function getFriendNoti() {
		// get all info the friend has
		$sql = 'SELECT u.uid, u.firstname, u.lastname, u.photo FROM friend f, user u WHERE f.friend_uid = u.uid AND is_valid = 0 AND f.uid = ' . $this->uid . ';';

		$query = mysqli_query($this->conn, $sql);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		return $data;
	}

	/**
	 * [getNeiNoti get neighbor notification]
	 * @return [type] [description]
	 */
	private function getNeiNoti() {
		$sql = 'SELECT u.uid, u.firstname, u.lastname, u.photo FROM neighbor n, user u WHERE n.neighbor_uid = u.uid AND is_valid = 0 AND n.uid = ' . $this->uid . ';';

		$query = mysqli_query($this->conn, $sql);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		return $data;
	}

	/**
	 * [getJoinBlockNoti get join block notification]
	 * @return [type] [description]
	 */
	private function getJoinBlockNoti() {
		$sql = 'WITH related_apply AS (SELECT jbu.uid, jba.joinid, jba.uid as apply_uid FROM join_block jba, join_block jbu WHERE jba.is_approved=0 AND jba.bid = jbu.bid AND jbu.is_approved = 1 AND jbu.uid = ' . $this->uid . ') SELECT u.uid, u.firstname, u.lastname, u.photo FROM related_apply ra, user u WHERE ra.apply_uid = u.uid AND (ra.uid, ra.joinid) not IN (SELECT uid, joinid FROM agree_join);';

		$query = mysqli_query($this->conn, $sql);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		return $data;
	}
}

try {
	$thisClass = new getNotification;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>