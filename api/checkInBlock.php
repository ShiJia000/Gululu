<?php
/**
 * http://localhost/db_nextdoor/api/checkInBlock
 */
require_once 'api.php';
class checkInBlock extends api {

	protected $bolCheckLogin = false;

	public function doExecute() {
		// in case of sql injection
		$conn = $this->conn;
		// $this->uid = intval($_GET['uid']);
		$this->uid = intval($_COOKIE['uid']);

		//check not NULL
		$this->checkNotNull();

		$check = 'SELECT jb.bid, b.bname, jb.is_approved FROM join_block jb, block b WHERE jb.bid = b.bid AND jb.uid=' . $this->uid . ' AND (jb.is_approved = 0 OR jb.is_approved = 1);';


		$query = $conn->query($check);
		$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

		return $checkData;
	}

	/**
	 * [checkNotNull description]
	 * @return [type] [void]
	 */
	public function checkNotNull(){
		if (!$this->uid) {
			throw new Exception("uid cannot be NUll!");
		}	}
}

try {
	$thisClass = new checkInBlock;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>