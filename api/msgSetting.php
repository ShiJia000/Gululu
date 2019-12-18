<?php
/**
 * http://localhost/db_nextdoor/api/msgSetting
 * msg setting api
 */

require_once 'api.php';
class msgSetting extends api{

	public function doExecute(){
		$conn = $this->conn;
		$this->uid = intval($_POST['uid']);

		$this->checkNotNull();

		//check has the user
		$check = "SELECT * FROM msg_setting WHERE uid = " . $this->uid . ";";
		$query = $conn->query($check);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($data) != 0) {
			throw new Exception("User exists!");
		}

		//
		$sql = "INSERT INTO msg_setting (`uid`,`tid`) VALUES 
		(" . $this->uid . ",1),
		(" . $this->uid . ",2),
		(" . $this->uid . ",3),
		(" . $this->uid . ",4),
		(" . $this->uid . ",5);";

		$data = $conn->query($sql);

		if ($data == 1) {
			return $data;
		} else {
			throw new Exception("An error Occurred!");
		}
	}

	/**
	 * [checkNotNull description]
	 * @return [type] [void]
	 */
	public function checkNotNull() {
		if (!$this->uid) {
			throw new Exception("uid cannot be null!");
		}
	}
}

try {
	$thisClass = new msgSetting;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>