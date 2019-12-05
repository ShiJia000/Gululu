<?php
/**
 * http://localhost/db_nextdoor/api/getNeighborInfo?uid=2
 */
require_once 'api.php';
class getNeighborInfo extends api {
	public function doExecute() {
		// in case of sql injection
		$uid=intval($_GET['uid']);

		$neighbor='SELECT n.uid, n.neighbor_uid as neighbor_id, u.firstname, u.lastname, u.photo 
		FROM neighbor n, user u
		WHERE u.uid=n.neighbor_uid
		AND n.uid=' . $uid . ';';

		$query = mysqli_query($this->conn, $neighbor);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if ($data) {
			return $data;
		} else {
			throw new Exception("No neighbors.");
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
$neighbors = new getNeighborInfo;
$data = $neighbors->getJson();
?>