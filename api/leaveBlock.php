<?php
/**
 * http://localhost/db_nextdoor/api/leaveBlock
 */
require_once 'api.php';
class leaveBlock extends api {

	public function doExecute() {
		// in case of sql injection
		$conn = $this->conn;
		$this->uid = intval($_COOKIE['uid']);
		$this->bid = intval($_POST['bid']);

		//check not NULL
		$this->checkNotNull();
		
		//check if the person in the block
		$check = 'SELECT * FROM (SELECT joinid FROM join_block WHERE is_approved = 1 AND uid = ' . $this->uid . ') as a, (SELECT joinid FROM join_block WHERE is_approved = 1 AND bid = ' . $this->bid . ') as b WHERE a.joinid = b.joinid;';
		$query = $conn->query($check);
		$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($checkData) == 0){
			throw new Exception("Error, the user not in the block!");
		}

		$leave_block = "UPDATE join_block SET is_approved=-1 WHERE uid = " . $this->uid . " AND bid = " . $this->bid . ";";
		$data = $conn->query($leave_block);

		if ($data == 1) {
			return $data;
		} else {
			throw new Exception("error, cannot leave the block!");
		}
	}

	/**
	 * [checkNotNull description]
	 * @return [type] [void]
	 */
	public function checkNotNull(){
		if (!$this->uid) {
			throw new Exception("uid cannot be NUll!");
		}
		if (!$this->bid) {
			throw new Exception("bid cannot be NUll!");
		}
	}
}

try {
	$thisClass = new leaveBlock;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>