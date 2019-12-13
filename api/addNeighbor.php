<?php
/**
 * http://localhost/db_nextdoor/api/addNeighbor
 */

require_once 'api.php';
class addNeighbors extends api {

	protected $bolCheckLogin = false;

	public function doExecute() {
		// in case of sql injection
		$conn = $this->conn;
		// $this->uid = intval($_POST['uid']);

		$this->uid = intval($_COOKIE['uid']);
		$this->neighbor_uid = intval($_POST['neighbor_uid']);
		$this->is_valid = intval($_POST['is_valid']);

		$this->checkNotNull();

		//check duplicate relationship
		$check_duplicate = 'SELECT * FROM neighbor WHERE uid = ' . $this->uid . ' AND neighbor_uid = ' . $this->neighbor_uid. ' AND is_valid = ' . $this->is_valid . ';';

		$query = $conn->query($check_duplicate);
		$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($checkData) != 0){
			throw new Exception("Error, exists duplicate relationship!");
		}


		//cancel / add neighbors
		if ($this->is_valid == -1){
			//check non-existent relationship
			$check = 'SELECT * FROM neighbor WHERE uid = ' . $this->uid . ' AND neighbor_uid = ' . $this->neighbor_uid. ';';

			$query = $conn->query($check);
			$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

			if (count($checkData) == 0){
				throw new Exception("Error, cannot cancel non-existent relationship");
			}

			//cancel neighbor
			$neighbors = 'UPDATE neighbor SET is_valid = -1 WHERE uid=' . $this->uid . ' AND neighbor_uid=' . $this->neighbor_uid . ';';

			$data = $conn->query($neighbors);


		} else if ($this->is_valid == 1){
			//update relationship or insert a new relationship
			$check = 'SELECT * FROM neighbor WHERE uid = ' . $this->uid . ' AND neighbor_uid = ' . $this->neighbor_uid. ' AND is_valid = -1 ;';

			$query = $conn->query($check);
			$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

			if (count($checkData) != 0){
				//update adding a neighbor
				$neighbors = 'UPDATE neighbor SET is_valid = 1 WHERE uid=' . $this->uid . ' AND  neighbor_uid = ' . $this->neighbor_uid . ';';

				$data = $conn->query($neighbors);
				
			}else{
				//check uid & neighbor not in the same block
				$check_hood = 'SELECT * FROM (SELECT bid FROM join_block WHERE uid=' . $this->uid . ' AND is_approved = 1) as a, join_block j WHERE j.uid=' . $this->neighbor_uid . ' AND is_approved = 1 AND a.bid = j.bid;';

				$query = $conn->query($check_hood);
				$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

				if (count($checkData) == 0){
					throw new Exception("Error, Cannot add because not in the same block!");
				}

				//add a new neighbor
				$neighbors = 'INSERT INTO neighbor (`uid`, `neighbor_uid`, `is_valid`) VALUES (' . $this->uid . ', ' . $this->neighbor_uid . ', 1);';

				$data = $conn->query($neighbors);
			}
		}
		else{
			throw new Exception("Error, invalid is_valid");
		}

		if ($data) {
			return $data;
		} else {
			throw new Exception("No neighbors.");
		}
	}

	/**
	 * [checkNotNull description]
	 * @return [type] [void]
	 */
	public function checkNotNull(){
		if (!$this->uid) {
			throw new Exception("uid cannot be NULL!");
		}
		if (!$this->neighbor_uid){
			throw new Exception("neighbor_uid cannot be NULL!");
		}
		if (!$this->is_valid){
			throw new Exception("is_valid cannot be NULL!");
		}
	}

}

try {
	$thisClass = new addNeighbors;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}
?>