<?php
/**
 * http://localhost/db_nextdoor/api/searchKeywordsOfMessage
 */

require_once 'api.php';
class searchKeywordsOfMessage extends api{

	public function doExecute(){
		$conn = $this->conn;
		$this->searchText = $this->check($_GET['searchText']);
		$this->searchType = $this->check($_GET['searchType']);

		$this->checkNotNull();

		if($this->searchType == "msg"){

			$check_reply = "SELECT u.firstname, u.lastname, u.photo, m.* FROM message m, user u WHERE u.uid = m.uid AND (u.firstname LIKE '%" . $this->searchText . "%' OR m.subject LIKE '%" . $this->searchText . "%' OR m.text_body LIKE '%" . $this->searchText . "%' OR m.title LIKE '%" . $this->searchText . "%');";

		}else if($this->searchType == "location"){

			$check_reply = "SELECT u.firstname, u.lastname, u.photo, m.* FROM message m, user u WHERE u.uid = m.uid AND m.address LIKE '%" . $this->searchText . "%';";
		}


		$query = $conn->query($check_reply);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		foreach ($data as $key => $value) {
			$mid = $value['mid'];
			$uid = $value['uid'];
			$replyMessage = 'SELECT u.firstname, u.lastname, u.photo, r.* FROM receive_reply rr, reply r, user u WHERE rr.rid = r.rid AND r.uid = u.uid AND rr.uid= ' . $uid . ' AND r.mid = ' .$mid . ';';
			$query = mysqli_query($this->conn, $replyMessage);
			$replyData = mysqli_fetch_all($query, MYSQLI_ASSOC);
			$data[$key]['reply'] = $replyData;
		}

		return $data;

	}
	
	/**
	 * [checkNotNull description]
	 * @return [type] [void]
	 */
	public function checkNotNull() {
		if (!$this->searchText) {
			throw new Exception("searchText cannot be null!");
		}
		if (!$this->searchType) {
			throw new Exception("searchType cannot be null!");
		}
	}
}

try {
	$thisClass = new searchKeywordsOfMessage;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}
?>