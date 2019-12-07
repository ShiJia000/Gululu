<?php
/**
 * @author     (Jia Shi <js11182@nyu.edu>)
 * http://localhost/db_nextdoor/api/getTypes
 */

require_once 'api.php';
class getTypes extends api {
	public function doExecute() {
		$conn = $this->conn;

		$sql = "SELECT * FROM type;";
		$query = $conn->query($sql);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if (count($data) == 0) {
			throw new Exception("No types!", -1);
		}
		return $data;
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
$getTypes = new getTypes;
$data = $getTypes->getJson();
?>