<?php
/**
 * @author     (Jia Shi <js11182@nyu.edu>)
 * http://localhost/db_nextdoor/api/getUserInBlock
 */

require_once 'api.php';
class getUserInBlock extends api {

	public function doExecute() {
		$conn = $this->conn;
		$uid = intval($_COOKIE['uid']);

		$sql = "SELECT u.uid, u.firstname, u.lastname, u.address, u.latitude, u.longitude FROM join_block jb, user u WHERE jb.uid = u.uid AND jb.is_approved = 1 AND jb.bid = (SELECT jb.bid FROM user, join_block WHERE user.uid = join_block.uid AND join_block.is_approved = 1 AND user.uid = " . $uid . ")";
		
		$query = $conn->query($sql);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($data) == 0) {
			throw new Exception("No types!", -1);
		}
		return $data;
	}
}

try {
	$thisClass = new getUserInBlock;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}


?>