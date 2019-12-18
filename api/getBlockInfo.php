<?php
/**
 * http://localhost/db_nextdoor/api/getBlockInfo
 */
require_once 'api.php';
class getBlockInfo extends api {

	public function doExecute() {
		// in case of sql injection

		$block = 'SELECT h.*, b.* FROM block b, hood h WHERE b.hid = h.hid order by h.hid;';

		$query = mysqli_query($this->conn, $block);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		$result = array();
		// $blockArr = array();
		$flag = 0;
		foreach ($data as $key => $value) {
			if ($key > 0 && $value["hid"] !== $data[$key - 1]["hid"] || $key === 0){
				$temp = array();
				$temp['hid'] = $value["hid"];
				$temp['hname'] = $value['hname'];
				$temp['blocks'] = array();
				array_push($result, $temp);
				$flag = count($result) - 1;
			}
			$oneBlock = array();
			$oneBlock['bid'] = $value['bid'];
			$oneBlock['bname'] = $value['bname'];
			array_push($result[$flag]['blocks'], $oneBlock);
			
		}

		// var_dump($result);


		if ($result) {
			return $result;
		} else {
			throw new Exception("No blocks.");
		}
	}
}

try {
	$thisClass = new getBlockInfo;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>