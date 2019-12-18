<?php
/**
 * http://localhost/db_nextdoor/api/msgToRead
 */
require_once 'api.php';
class msgToRead extends api {

	public function doExecute() {
		// in case of sql injection
		$conn = $this->conn;
		$uid = intval($_COOKIE['uid']);

		$update = 'UPDATE receive_msg SET is_read = 1 WHERE uid = ' . $uid . ';';
		$data = $conn->query($update);

		return $data;
	}
}

try {
	$thisClass = new msgToRead;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>