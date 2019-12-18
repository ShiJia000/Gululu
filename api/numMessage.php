<?php
/**
 * http://localhost/db_nextdoor/api/numMessage
 */
require_once 'api.php';
class numMessage extends api {

	public function doExecute() {
		// in case of sql injection
		$conn = $this->conn;
		$this->uid = intval($_COOKIE['uid']);
		
		$message = 'SELECT count(*) as num FROM receive_msg WHERE is_read = 0 AND uid=' . $this->uid . ';';

		$query = mysqli_query($this->conn, $message);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if ($data) {
			return $data[0];
		} else {
			throw new Exception("No message.");
		}
	}
}

try {
	$thisClass = new numMessage;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>