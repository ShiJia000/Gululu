<?php
/**
 * http://localhost/db_nextdoor/api/searchKeywordsOfMessage
 */

require_once 'api.php';
class searchKeywordsOfMessage extends api{
	protected $bolCheckLogin = false;
	public function doExecute(){
		$conn = $this->conn;
		$this->keywords = $this->check($_POST['keywords']);

		$this->checkNotNull();

		$check_reply = "SELECT * FROM message WHERE subject LIKE '%" . $this->keywords . "%' OR text_body LIKE '%" . $this->keywords . "%' OR title LIKE '%" . $this->keywords . "%';";
		$query = $conn->query($check_reply);
		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		if($data){
			return $data;
		}else{
			throw new Exception("No such messages!");
		}
	}
	
	/**
	 * [checkNotNull description]
	 * @return [type] [void]
	 */
	public function checkNotNull() {
		if (!$this->keywords) {
			throw new Exception("keywords cannot be null!");
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