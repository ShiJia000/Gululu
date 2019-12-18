<?php
/**
 * http://localhost/db_nextdoor/api/customizeSetting
 * customized setting api
 */

require_once 'api.php';
class custSetting extends api{

	public function doExecute(){
		$conn = $this->conn;
		$this->uid = intval($_POST['uid']);
		$this->tid = intval($_POST['tid']);

		$this->checkNotNull();


		//check if exists the user
		$check = "SELECT * FROM msg_setting WHERE uid = " . $this->uid . " AND tid = " . $this->tid . ";";
		$query = $conn->query($check);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);


		if (count($data) != 0) {
			$sql = "DELETE FROM msg_setting WHERE uid = " . $this->uid . " AND tid = " . $this->tid . ";";

			$data = $conn->query($sql);
		} else{
			$sql = "INSERT INTO msg_setting (`uid`,`tid`) VALUES 
			(" . $this->uid . ", " . $this->tid . ");";

			$data = $conn->query($sql);
		}

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
		if (!$this->tid) {
			throw new Exception("tid cannot be null!");
		}
	}

}

try {
	$thisClass = new custSetting;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>