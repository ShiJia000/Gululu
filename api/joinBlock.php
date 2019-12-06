<?php
/**
 * http://localhost/db_nextdoor/api/joinBlock
 */
require_once 'api.php';
class joinBlock extends api {
	public function doExecute() {
		// in case of sql injection
		$conn = $this->conn;
		$this->uid = intval($_POST['uid']);
		$this->bid = intval($_POST['bid']);

		//check not NULL
		$this->checkNotNull();

		//check if the block is empty
		$check_empty_block='SELECT count(*) as cn
		FROM join_block
		WHERE is_approved<>-1
		AND bid=' . $this->bid  . ';';

		$query = $conn->query($check_empty_block);
		$data_block = mysqli_fetch_all($query, MYSQLI_ASSOC);


		//check if the user in the block or in other blocks
		$check_user_in_block='SELECT count(*) as cn
		FROM join_block
		WHERE is_approved<>-1
		AND uid=' . $this->uid  . ';';

		$query = $conn->query($check_user_in_block);
		$data_user = mysqli_fetch_all($query, MYSQLI_ASSOC);


		if ($data_block[0]["cn"] === "0" && !$data_user[0]["cn"]){
			//empty block and new user
			$j_block="INSERT INTO join_block (`uid`, `bid`, `is_approved`, `approve_num`)VALUES (".$this->uid.",".$this->bid.",1,0);";
		}
		else if ($data_block[0]["cn"] !== "0" && !$data_user[0]["cn"]){
			//non-empty block but new user
			$j_block="INSERT INTO join_block (`uid`, `bid`, `is_approved`, `approve_num`)VALUES (".$this->uid.",".$this->bid.",0,0);";
		}
		else{
			throw new Exception("already exist the user!");
		}

		$data = $conn->query($j_block);
		if ($data==1) {
			return $data;
		} else {
			throw new Exception("error, cannot join the block!");
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
		if (!$this->bid){
			throw new Exception("bid cannot be NULL!");
		}
	}
	
	public function getJson() {
		try {
			$this->res['data'] = $this->doExecute();
		} catch (Exception $e) {
			$this->res['status'] = -1;
			$this->res['message'] = $e->getMessage();
		} finally {
			echo json_encode($this->res);
		}
	}
}
$joinblock = new joinBlock;
$data = $joinblock->getJson();
?>