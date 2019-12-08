<?php
/**
 * @author     (Jia Shi <js11182@nyu.edu>)
 * update profile api
 */

require_once 'api.php';

class updateProfile extends api {

	protected $bolCheckLogin = false;

	public function doExecute() {
		$conn = $this->conn;

		// check not null
		if (!isset($_POST['selfIntro']) || !isset($_POST['familyIntro'])) {
			throw new Exception("The request must have the selfIntro and familyIntro parameters!");
		}

		// in case of sql injection
		$selfIntro = $this->check($_POST['selfIntro']);
		$familyIntro = $this->check($_POST['familyIntro']);
		$uid = intval($_POST['uid']);

		$sql = "UPDATE user SET self_intro = '" . $selfIntro . "', family_intro = '" . $familyIntro . "' WHERE uid = " . $uid . ";";
		$data = $conn->query($sql);

		if ($data == 1) {
			// get user info 
			$sql = "SELECT user.* FROM user WHERE uid = " . $uid . ";";
			$query = $conn->query($sql);
			$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

			return $data[0];
		} else {
			throw new Exception("An error Occurred!");
		}
	}
}

try {
	$thisClass = new updateProfile;
	$thisClass->res['data'] = $thisClass->doExecute();

} catch (Exception $e) {
	$thisClass->res['status'] = $e->getCode() ? $e->getCode() : -1;
	$thisClass->res['message'] = $e->getMessage();

} finally {
	echo json_encode($thisClass->res);
}

?>