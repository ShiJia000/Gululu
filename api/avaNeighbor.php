<?php
/**
 * http://localhost/db_nextdoor/api/avaNeighbor?uid=2
 */
require_once 'api.php';
class avaNeighbor extends api {

	protected $bolCheckLogin = false;

	public function doExecute() {
		// in case of sql injection

		$uid = intval($_GET['uid']);
		// $uid = intval($_COOKIE['uid']);
		
		$sql = 'SELECT distinct u.uid, u.firstname, u.lastname, jb.bid FROM user u, join_block jb,(SELECT uid, bid FROM join_block WHERE uid=' . $uid . ' AND is_approved=1) as t WHERE u.uid = jb.uid AND jb.bid = t.bid AND is_approved=1 AND u.uid<>' . $uid . ' AND u.uid NOT IN (SELECT neighbor_uid FROM neighbor WHERE uid=' . $uid . ' AND is_valid=1);';


		// var_dump($sql);

		$query = mysqli_query($this->conn, $sql);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if ($data) {
			return $data;
		} else {
			throw new Exception("No neighbors.");
		}
	}
}

try {
	$thisClass = new avaNeighbor;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>