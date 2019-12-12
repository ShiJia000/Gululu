<?php
/**
 * http://localhost/db_nextdoor/api/neighborNotification
 * Receive Notification api
 */

require_once 'api.php';
class neighborNotification extends api{
	protected $bolCheckLogin = false;
	public function doExecute(){
		$uid = intval($_GET['uid']);

		$sql = 'SELECT * FROM neighbor WHERE uid = ' . $uid  . ';';

		$query = mysqli_query($this->conn, $sql);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if ($data) {
			return $data;
		} else {
			throw new Exception("An error Occurred!");
		}
	}
}

try {
	$thisClass = new neighborNotification;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>