<?php
/**
 * http://localhost/db_nextdoor/api/updateBlock
 */

require_once 'api.php';
class updateBlock extends api {

	protected $bolCheckLogin = false;

	public function doExecute(){
		$conn = $this->conn;
		$conn->autocommit(false);

		$this->uid = intval($_POST['uid']);
		$this->joinid = intval($_POST['joinid']);
		$this->is_agree = intval($_POST['is_agree']);

		$this->checkNotNull();

		//check already added in the block
		$check_added = 'SELECT * FROM join_block WHERE joinid = ' . $this->joinid . ' AND is_approved = 1;';

		$query = $conn->query($check_added);
		$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($checkData) == 1){
			throw new Exception("Error, already exist!");
		}


		//check not in the same block
		$check_uid = 'SELECT bid FROM join_block WHERE uid = ' . $this->uid . ' AND is_approved = 1;';

		$query = $conn->query($check_uid);
		$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

		$check_uid2 = 'SELECT * FROM join_block WHERE joinid = ' . $this->joinid . ' AND bid = ' . $checkData[0]['bid'] . ';';	

		$query = $conn->query($check_uid2);
		$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($checkData) == 0){
			throw new Exception("Error, not in the same block!");
		}


		//check if uid&joinid in agree_join
		$check_duplicate = 'SELECT * FROM agree_join WHERE uid=' . $this->uid . ' AND joinid=' . $this->joinid . ';';

		$query = $conn->query($check_duplicate);
		$checkData = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($checkData) != 0){
			throw new Exception("Already making a decision!");
		}

		//insert data into agree_join
		if ($this->is_agree === 1 or $this->is_agree === -1){
			$insert_sql = "INSERT INTO agree_join (`uid`, `joinid`, `is_agree`) VALUES (" . $this->uid . ", " . $this->joinid . ", " . $this->is_agree . ");";

			$query = $conn->query($insert_sql);

			if ($this->is_agree === 1){
				//check num of agreements
				$count_sql = 'SELECT * FROM agree_join WHERE joinid = ' . $this->joinid . ' AND is_agree=1;';

				$query2 = $conn->query($count_sql);
				$checkData = mysqli_fetch_all($query2, MYSQLI_ASSOC);

				//get num of people in one block
				$sql = "SELECT * FROM join_block j, (SELECT bid FROM join_block WHERE uid= " . $this->uid . ") as t WHERE j.is_approved = 1 AND t.bid = j.bid;";

				$query3 = $conn->query($sql);
				$checkData2 = mysqli_fetch_all($query3, MYSQLI_ASSOC);

				//if query2>3 or query2=total(uid in block)
				if (count($checkData) >= 3 or count($checkData) == count($checkData2)){
					//update is_approved & approve_num
					$update_num = 'UPDATE JOIN_BLOCK SET approve_num = approve_num + 1 WHERE joinid = ' . $this->joinid . ';';

					$update_is_app = 'UPDATE JOIN_BLOCK SET is_approved = 1 WHERE joinid = ' . $this->joinid . ';';

					$conn->query($update_num);
					$conn->query($update_is_app);

				}else {
					//update approve_num
					$update_num = 'UPDATE JOIN_BLOCK SET approve_num = approve_num + 1 WHERE joinid = ' . $this->joinid . ';';

					$conn->query($update_num);
				}
			}
		}
		else{
			throw new Exception("error!");
		}

		// transaction
		if (!$conn->errno) {
		    $conn->commit();
		    return 'Success';
		} else {
		    $conn->rollback();
		    throw new Exception("An error Occurred!");
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
		if (!$this->joinid){
			throw new Exception("joinid cannot be NULL!");
		}
		if(!$this->is_agree){
			throw new Exception("is_agree cannot be NULL");
		}
	}
}

try {
	$thisClass = new updateBlock;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>