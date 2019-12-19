<?php
/**
 * http://localhost/db_nextdoor/api/getUserInfo
 */
require_once 'api.php';

class getUserInfo extends api {

	public function doExecute() {
		// in case of sql injection
		$conn = $this->conn;
		$this->uid = intval($_COOKIE['uid']);

		$sql='SELECT photo FROM user WHERE uid = ' . $this->uid . ';';

		$query = mysqli_query($this->conn, $sql);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		return $data[0];
	}
}

try {
	$friendFeed = new getUserInfo;
	$friendFeed->res['data'] = $friendFeed->doExecute();

} catch (Exception $e) {
	$friendFeed->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$friendFeed->res['message'] = $e->getMessage();

} finally {
	echo json_encode($friendFeed->res);
}

?>