<?php
/**
 * @author     (Jia Shi <js11182@nyu.edu>)
 * sign in api
 */

require_once 'api.php';

class getProfile extends api {

	protected $bolCheckLogin = true;

	public function doExecute() {
		$conn = $this->conn;

		// in case of sql injection
		$this->uid = intval($_COOKIE['uid']);


		// get block info 
		$sql = "SELECT u.uid, u.firstname, u.lastname, u.self_intro, u.family_intro, b.bname FROM join_block jb, user u, block b WHERE jb.uid = u.uid AND jb.bid = b.bid AND is_approved = 1 AND u.uid = " . $this->uid .";";
		$query = $conn->query($sql);

		$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

		return $data[0];
	}
}

try {
	$thisClass = new getProfile;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>
