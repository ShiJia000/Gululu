<?php
/**
 * http://localhost/db_nextdoor/api/getNeighborInfo?uid=2
 */
require_once 'api.php';
class getNeighborInfo extends api {

	protected $bolCheckLogin = false;

	public function doExecute() {
		// in case of sql injection
		$uid = intval($_GET['uid']);

		$neighbor = 'SELECT n.uid, n.neighbor_uid as neighbor_id, u.firstname, u.lastname, u.photo FROM neighbor n, user u WHERE u.uid=n.neighbor_uid AND n.uid=' . $uid . ';';

		$query = mysqli_query($this->conn, $neighbor);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if ($data) {
			return $data;
		} else {
			throw new Exception("No neighbors.");
		}
	}
}

try {
	$thisClass = new getNeighborInfo;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>